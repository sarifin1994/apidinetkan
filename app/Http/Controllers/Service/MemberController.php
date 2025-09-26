<?php

namespace App\Http\Controllers\Service;

use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
use App\Exports\MembersExport;
use App\Models\Member;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\Transaksi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RadiusSession;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\MemberRequest;
use App\Imports\MembersImport;
use App\Models\PppoeMember;
use App\Models\User;
use Illuminate\Support\Facades\Process;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Data\Services\MemberService;

class MemberController extends Controller
{
    public function __construct(
        protected MemberService $dataService
    ) {}

    public function index(Request $request)
    {
        if (request()->ajax()) {
            $members = Member::query()
                ->where('group_id', $request->user()->id_group)
                ->with('ppp:id,username,status');

            return DataTables::of($members)
                ->addIndexColumn()
                ->editColumn('ppp_username', function ($row) {
                    return $row->ppp->username;
                })
                ->editColumn('ppp_status', function ($row) {
                    return $row->ppp->status;
                })
                ->toJson();
        }

        return abort(404);
    }

    public function store(MemberRequest $request)
    {
        $request->validated();

        $id = !empty($request->id_member) ? $request->id_member : $this->dataService->generateMemberId($request->user());
        $member = Member::create([
            'group_id' => $request->user()->id_group,
            'id_member' => $id,
            'id_member_new' => $this->dataService->first_id_member($request->user())."".$id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'wa' => $request->wa,
            'address' => $request->address,
            'status' => $request->status,
            'no_ktp' => $request->no_ktp,
            'npwp' => $request->npwp,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'province_id' =>$request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id
        ]);

        return response()->json(['success' => 'Data is successfully added']);
    }

    public function show(string $id)
    {
        $member = Member::query()
            ->where('group_id', request()->user()->id_group)
            ->where('id', $id)
            ->first();

        return response()->json($member);
    }

    public function update(MemberRequest $request, string $id)
    {
        $request->validated();

        $member = Member::find($id);
        $member->update([
            'id_member' => !empty($request->id_member) ? $request->id_member : $this->dataService->generateMemberId($request->user()),
            'full_name' => $request->full_name,
            'email' => $request->email,
            'wa' => $request->wa,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function services(string $id)
    {
        if (!request()->ajax()) {
            abort(404);
        }

        $services = PppoeMember::query()
            ->where('member_id', $id)
            ->with(['pppoe:id,username,nas,status,member_name,kode_area,kode_odp', 'pppoe.session:username,session_id,ip,status']);

        return DataTables::of($services)
            ->addIndexColumn()
            ->editColumn('session_internet', function ($row) {
                return $row->pppoe->session->session_id;
            })
            ->toJson();
    }

    public function serviceList(Request $request, string $memberId)
    {
        $services = PppoeUser::where('group_id', $request->user()->id_group)
            ->whereHas('data', function ($query) use ($memberId) {
                $query->where('member_id', $memberId);
            })
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->data->id_service . ' - ' . $service->data->member->full_name . ' - ' . $service->profile,
                ];
            });

        return response()->json($services);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:frradius.member,id',
        ]);

        Member::destroy($data['ids']);

        return response()->json(['success' => 'Data is successfully deleted']);
    }

    public function invoices(string $id)
    {
        if (!request()->ajax()) {
            abort(404);
        }

        $invoices = Invoice::query()
            ->where('member_id', $id);

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('price', function ($row) {
                $ppn = $row->ppn / 100;
                $discount = $row->discount / 100;
                return number_format($row->price + ($row->price * $ppn) - ($row->price * $discount));
            })
            ->toJson();
    }

    public function listOptions(Request $request)
    {
        $members = Member::query()
            ->where('group_id', $request->user()->id_group)
            ->get(['id', 'full_name']);

        return response()->json($members);
    }

    public function sample()
    {
        $data = [
            ['id_member', 'name', 'email', 'wa', 'address'],
            ['2401580095', 'Budi', 'email@mail.com', '6281234567890', 'Budi address'],
        ];

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, 'members_import_sample.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new MembersImport, $request->file('file'));

        $logs = blink()->get('import_logs');

        return response()->json([
            'message' => 'Data is successfully imported',
            'logs' => $logs
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new MembersExport($request->user()->id_group), 'members_export.xlsx');
    }
}

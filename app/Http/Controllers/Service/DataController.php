<?php

namespace App\Http\Controllers\Service;

use App\Enums\MemberStatusEnum;
use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
use App\Exports\ServicesExport;
use App\Models\Districts;
use App\Models\Nas;
use App\Models\Odp;
use App\Models\Area;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\License;
use App\Models\PppoeUser;
use App\Models\Province;
use App\Models\RadiusNas;
use App\Models\Regencies;
use App\Models\Transaksi;
use App\Models\PppoeProfile;
use App\Models\Villages;
use Illuminate\Http\Request;
use App\Models\RadiusSession;
use App\Models\BillingSetting;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Imports\ServicesImport;
use App\Models\PppoeMember;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Process;
use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Data\Services\PppoeService;

class DataController extends Controller
{
    public function __construct(
        protected PppoeService $pppoeService
    ) {}

    public function index(Request $request)
    {
        $groupId = $request->user()->id_group;

        if (request()->ajax()) {
            $users = PppoeUser::with([
                'member:id_service,pppoe_id,member_id,payment_type,billing_period,reg_date,next_due',
                'member.data:id,full_name',
                'radius:name,ip_router',
                'rprofile:id,name',
                'rarea:kode_area,id',
                'rodp:id,kode_odp',
                'session:username,session_id,ip,status'
            ])
                ->where('db_radius.user_pppoe.group_id', $groupId);

            return DataTables::of($users)
                ->addIndexColumn()
                ->editColumn('nas_name', fn($row) => $row->radius->name)
                ->editColumn('profile_name', fn($row) => $row->rprofile->name)
                ->editColumn('area_name', fn($row) => $row->rarea->kode_area)
                ->editColumn('odp_name', fn($row) => $row->rodp->kode_odp)
                ->editColumn('session_internet', fn($row) => $row->session->session_id)
                ->toJson();
        }

        list($totalUsers, $totalActive, $totalNew, $totalSuspend, $totalInactive) = $this->getUserStatistics($groupId);
        [$totalMembers, $totalActiveMembers, $totalNewMembers, $totalInactiveMembers] = $this->getMemberStatistics($groupId);

        $areas = Area::where('group_id', $groupId)
            ->select('id', 'kode_area')
            ->orderBy('kode_area', 'desc')
            ->get();
        $nas = Nas::where('group_id', $groupId)
            ->select('ip_router', 'name')
            ->get();
        $profiles = PppoeProfile::where('group_id', $groupId)
            ->select('id', 'name', 'price')
            ->get();
        $provinces = [];
        $regencies = [];
        $districts = [];
        $villages = [];


//        $provinces = Province::query()
//            ->orderBy('name', 'asc')
//            ->get();
//        $regencies = Regencies::query()
//            ->orderBy('name', 'asc')
//            ->get();
//        $districts = Districts::query()
//            ->orderBy('name', 'asc')
//            ->get();
//        $villages = Villages::query()
//            ->orderBy('name', 'asc')
//            ->get();

        return view('services.data.index', compact(
            'areas',
            'nas',
            'profiles',
            'totalUsers',
            'totalActive',
            'totalNew',
            'totalSuspend',
            'totalInactive',
            'totalMembers',
            'totalActiveMembers',
            'totalNewMembers',
            'totalInactiveMembers',
            'provinces',
            'regencies',
            'districts',
            'villages'
        ));
    }

    public function show(string $id)
    {
        $data = PppoeUser::with('radius', 'rprofile', 'rarea', 'rodp', 'member:member_id,pppoe_id', 'member.data:id,full_name')->find($id);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function invoices(string $id)
    {
        $invoices = Invoice::where('pppoe_id', $id)
            ->orderBy('id', 'desc');

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('price', function ($row) {
                $ppn = $row->ppn / 100;
                $discount = $row->discount / 100;
                return number_format($row->price + ($row->price * $ppn) - ($row->price * $discount));
            })
            ->toJson();
    }

    public function getKodeOdp(Request $request)
    {
        $odp = Odp::where('group_id', $request->user()->id_group)
            ->where('kode_area_id', $request->kode_area_id)
            ->orderBy('kode_odp')
            ->get(['kode_odp']);

        return response()->json(['odp' => $odp]);
    }

    public function getSession(Request $request)
    {
        $sessions = RadiusSession::where('shortname', $request->user()->shortname)
            ->where('username', $request->username)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($sessions);
    }

    public function getPrice(Request $request)
    {
        $data = PppoeProfile::where('group_id', $request->user()->id_group)
            ->where('id', $request->id)
            ->select(['price'])
            ->first();

        $data->price = intval($data->price);

        return response()->json($data);
    }

    public function getPpp(Request $request, string $id)
    {
        $data = PppoeUser::where('id', $id)
            ->with([
                'data:id,pppoe_id,member_id',
                'data.member:id,full_name,address',
                'session',
            ])
            ->first(['id', 'group_id', 'username', 'value', 'profile', 'member_name', 'status', 'kode_area', 'kode_odp', 'created_at']);

        return response()->json($data);
    }

    public function getPayment(Request $request, string $pppoeId)
    {
        $data = PppoeMember::with('profile', 'member:id,full_name')
            ->whereHas('pppoe', function ($query) use ($request) {
                $query->where('group_id', $request->user()->id_group);
            })
            ->where('pppoe_id', $pppoeId)
            ->first();

        return response()->json($data);
    }

    public function storePayment(Request $request, PppoeUser $user)
    {
        $request->validate([
            'member_id' => 'required|exists:frradius.member,id',
            'payment_type' => 'required|string',
            'billing_period' => 'required|string',
            'reg_date' => 'required|date',
            'next_due' => 'required|date',
            'profile_id' => 'required|exists:db_profile.profile_pppoe,id',
            'ppn' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $this->pppoeService->handleMemberUserCreation($request, $user);
        $member = PppoeMember::where('pppoe_id', $user->id)->first();

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Create')
            ->log('Create Billing Payment for service: ' . $user->username . '@' . $user->value . ' for member: ' . $member->data->full_name);

        return response()->json([
            'success' => true,
            'message' => 'Data Pembayaran Berhasil Dibuat',
        ]);
    }

    public function updatePayment(Request $request, PppoeUser $user)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:frradius.member,id',
            'payment_type' => 'required|string',
            'billing_period' => 'required|string',
            'reg_date' => 'required|date',
            'next_due' => 'required|date',
            'profile_id' => 'required|exists:db_profile.profile_pppoe,id',
            'ppn' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $member = $request->has('member_id') && (int) $request->member_id === $user->member->member_id
            ? $user->member
            : null;

        $profile = PppoeProfile::where('group_id', $request->user()->id_group)
            ->where('id', $request->profile_id)
            ->first();

        $user->update([
            'profile' => $profile->name,
        ]);

        if (!$member) {
            $member = $this->pppoeService->handleMemberUserCreation($request, $user);

            activity()
                ->tap(function (Activity $activity) use ($request) {
                    $activity->group_id = $request->user()->id_group;
                })
                ->event('Create')
                ->log('Create Billing Payment for service: ' . $user->username . '@' . $user->value . ' for member: ' . $member->data->full_name);

            return response()->json([
                'success' => true,
                'message' => 'Data Pembayaran Berhasil Disimpan',
            ]);
        }

        $tgl = date('d', strtotime($request->next_due));

        if ($request->billing_period === 'Fixed Date') {
            $member->update([
                'next_due' => $request->next_due,
                'next_invoice' => $request->next_due,
                'tgl' => $tgl,
                'ppn' => $request->ppn,
                'discount' => $request->discount,
            ]);
        } elseif ($request->billing_period === 'Billing Cycle') {
            $nextInvoice = Carbon::createFromFormat('Y-m-d', $request->next_due)
                ->startOfMonth()
                ->toDateString();

            $member->update([
                'next_due' => $request->next_due,
                'next_invoice' => $nextInvoice,
                'ppn' => $request->ppn,
                'discount' => $request->discount,
            ]);
        }

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Update Billing Payment: ' . $request->full_name . '');

        return response()->json([
            'success' => true,
            'message' => 'Data Pembayaran Berhasil Diupdate',
        ]);
    }

    public function enable(Request $request, PppoeUser $id)
    {
        return $this->updateUserStatusAndDisconnect($request, $id, 1, "Enable User PPPoE");
    }

    public function disable(Request $request, PppoeUser $id)
    {
        return $this->updateUserStatusAndDisconnect($request, $id, 0, "Disable User PPPoE");
    }

    public function suspend(Request $request, PppoeUser $id)
    {
        return $this->updateUserStatusAndDisconnect($request, $id, 2, "Suspend User PPPoE");
    }

    public function store(Request $request)
    {
        $groupId = $request->user()->id_group;
        $this->checkLicenseLimit($groupId, $request->user()->license_id);

        $validator = Validator::make($request->all(), [
            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:255',
                Rule::unique('db_radius.user_pppoe')->where('group_id', $groupId)
            ],
            'password' => 'nullable|string',
            'profile' => 'required',
            'member_id' => 'nullable|exists:frradius.member,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user = $this->createPppoeUser($request, 1);

        if ($request->include_payment == true) {
            $this->pppoeService->handleMemberUserCreation($request, $user);
        }

        $this->logActivity($request, 'Create', 'Create New User PPPoE: ' . $request->username);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
        ]);
    }

    public function update(Request $request, PppoeUser $data)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:5',
            'password' => 'required|string|min:2',
            'member_id' => 'nullable|exists:frradius.member,id',
            'profile' => 'required',
            'profile_id' => 'required|exists:db_profile.profile_pppoe,id',
            'ip_address' => 'nullable',
            'nas' => 'nullable',
            'kode_area' => 'required|string',
            'kode_odp' => 'nullable|string',
            'lock_mac' => 'required|in:0,1',
            'mac' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $updateData = [
            'username' => $request->username,
            'value' => $request->password,
            'profile' => $request->profile,
            'ip_address' => $request->ip_address,
            'nas' => $request->nas,
            'kode_area' => $request->kode_area,
            'kode_odp' => $request->kode_odp,
            'lock_mac' => $request->lock_mac,
        ];

        if ($request->lock_mac !== '0') {
            $updateData['mac'] = $request->mac;
        }

        $data->update($updateData);

        $updateData = [
            'kode_area' => $request->kode_area,
            'profile_id' => $request->profile_id,
        ];

        if ($request->member_id !== null) {
            $updateData['member_id'] = $request->member_id;
        }

        $member = PppoeMember::where('pppoe_id', $data->id)
            ->first();

        if ($member !== null) {
            $member->update($updateData);
        } else {
            throw new \Exception('Member record not found.');
        }

        $this->logActivity($request, 'Update', 'Update User PPPoE: ' . $request->username);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
        ]);
    }

    public function destroy(Request $request, string $id = '')
    {
        if ($id) {
            $user = PppoeUser::findOrFail($id);
            $username = PppoeUser::where('id', $id)->value('username');
            $member_id = PppoeMember::where('pppoe_id', $id)->value('id');

            // Delete radius sessions
            RadiusSession::where('shortname', $request->user()->shortname)
                ->where('username', $username)
                ->delete();

            // If user is associated with a member, delete related invoices and member record
            if ($member_id !== null) {
                Invoice::where('pppoe_id', $member_id)->delete();
                PppoeMember::where('pppoe_id', $id)->delete();
            }

            $user->delete();

            $this->logActivity($request, 'Delete', 'Delete User PPPoE: ' . $username);
        } else {
            $data = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|exists:db_radius.user_pppoe,id',
            ]);

            foreach ($data['ids'] as $id) {
                $user = PppoeUser::findOrFail($id);
                $username = PppoeUser::where('id', $id)->value('username');
                $member_id = PppoeMember::where('pppoe_id', $id)->value('id');

                // Delete radius sessions
                RadiusSession::where('shortname', $request->user()->shortname)
                    ->where('username', $username)
                    ->delete();

                // If user is associated with a member, delete related invoices and member record
                if ($member_id !== null) {
                    Invoice::where('pppoe_id', $member_id)->delete();
                    PppoeMember::where('pppoe_id', $id)->delete();
                }

                $user->delete();

                $this->logActivity($request, 'Delete', 'Delete User PPPoE: ' . $username);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function sample()
    {
        $data = [
            ['**Note:**', ''],
            ['* Pastikan isi dari profile, area, odp, type dan billing ada di situs atau  sistem akan membuatkan data baru berdasarkan isi tersebut.', ''],
            ['* Isi username dan password dengan mac apabila otentikasi melalui mac address', ''],
            ['* Isi type dapat berupa Prabayar atau Pascabayar', ''],
            ['* Isi billing dapat berupa Fixed Date dan Billing Cycle', ''],
            ['* Isi ppn dan discount harus berupa satuan angka bukan nominal atau angka dengan persentase', ''],
            ['* Format tanggal untuk active_data dan due_date yang digunakan adalah d/m/Y atau 01/09/2024', ''],
            ['* Hapus semua note ini dan baris-baris contoh data sebelum dan setelah header baris 10 ketika akan melakukan import', ''],
            [''],
            ['id_service', 'id_member', 'username', 'password', 'profile', 'nas', 'ip_address', 'type', 'lock_mac', 'area', 'odp', 'payment_type', 'billing', 'ppn', 'discount', 'active_date', 'due_date'],
            ['2401984985', '2401580095', 'test@network.net', 'password123', '10MBPS', '', '', 'pppoe', '', 'ARA', 'SG1', 'Prabayar', 'Fixed Date', '11', '0', '01/09/2024', '01/10/2024'],
            ['2401984986', '', '21:fa:16:97:d3:ce', '21:fa:16:97:d3:ce', '100MBPS', '', '', 'dhcp', '', '', '', '', '', '', '', ''],
            ['2401984987', '2401580095', 'test2@network.net', 'password123', '10MBPS', '', '', 'pppoe', '4a:fa:11:27:d4:ce', 'ARA2', 'SG1', 'Prabayar', 'Billing Cycle', '11', '0', '01/09/2024', '01/10/2024'],
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
        }, 'services_import_sample.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new ServicesImport, $request->file('file'));

        $logs = blink()->get('import_logs');

        return response()->json([
            'message' => 'Data is successfully imported',
            'logs' => $logs
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new ServicesExport($request->user()->id_group), 'services_export.xlsx');
    }

    public function getServiceStats(Request $request)
    {
        $groupId = $request->user()->id_group;
        [$totalUsers, $totalActive, $totalNew, $totalSuspend, $totalInactive] = $this->getUserStatistics($groupId);

        return response()->json([
            'total_users' => $totalUsers,
            'total_active' => $totalActive,
            'total_new' => $totalNew,
            'total_suspend' => $totalSuspend,
            'total_inactive' => $totalInactive,
        ]);
    }

    public function getMemberStats(Request $request)
    {
        $groupId = $request->user()->id_group;
        [$totalMembers, $totalActive, $totalNew, $totalInactive] = $this->getMemberStatistics($groupId);

        return response()->json([
            'total_members' => $totalMembers,
            'total_active' => $totalActive,
            'total_new' => $totalNew,
            'total_inactive' => $totalInactive,
        ]);
    }

    /**
     * Helper Methods
     */

    private function getUserStatistics($groupId)
    {
        $totalUsers = PppoeUser::where('group_id', $groupId)->count();
        $totalActive = PppoeUser::where('group_id', $groupId)->where('status', 1)->count();
        $totalNew = PppoeUser::where('group_id', $groupId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        $totalSuspend = PppoeUser::where('group_id', $groupId)->where('status', 2)->count();
        $totalInactive = PppoeUser::where('group_id', $groupId)->where('status', 0)->count();

        return [$totalUsers, $totalActive, $totalNew, $totalSuspend, $totalInactive];
    }

    private function getMemberStatistics($groupId)
    {
        $totalMembers = Member::where('group_id', $groupId)->count();
        $totalActive = Member::where('group_id', $groupId)->where('status', MemberStatusEnum::ACTIVE)->count();
        $totalNew = Member::where('group_id', $groupId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        $totalInactive = Member::where('group_id', $groupId)->where('status', MemberStatusEnum::INACTIVE)->count();

        return [$totalMembers, $totalActive, $totalNew, $totalInactive];
    }

    private function logActivity(Request $request, $event, $message)
    {
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event($event)
            ->log($message);
    }

    private function runRadclientDisconnect($request, $username)
    {
        if ($request->nas === null) {
            $nas = RadiusNas::where('group_id', $request->user()->id_group)
                ->select('nasname', 'secret')
                ->get();
            foreach ($nas as $item) {
                Process::path('/usr/bin/')->run("echo User-Name='$username' | radclient -r 1 {$item['nasname']}:3799 disconnect {$item['secret']}");
            }
        } else {
            Process::path('/usr/bin/')->run("echo User-Name='$username' | radclient -r 1 {$request->nas}:3799 disconnect {$request->secret}");
        }
    }

    private function updateUserStatusAndDisconnect(Request $request, PppoeUser $user, int $status, string $logMessage)
    {
        $user->update(['status' => $status]);
        $this->runRadclientDisconnect($request, $request->username);
        $this->logActivity($request, 'Update', "{$logMessage}: {$request->username}");

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
        ]);
    }

    private function checkLicenseLimit($groupId, $licenseId)
    {
        $currentCount = PppoeUser::where('group_id', $groupId)->count();
        $limit = License::where('id', $licenseId)->value('limit_pppoe');
        if ($currentCount >= $limit) {
            response()->json(['error' => 'Sorry your license is limited, please upgrade!'])->send();
            exit; // Ensure no further execution
        }
    }

    private function createPppoeUser(Request $request, $status): PppoeUser
    {
        $isDhcp = $request->type === 'dhcp';
        $username = $isDhcp ? $request->mac_address : $request->username;
        $value = $isDhcp ? $request->mac_address : $request->password;

        return PppoeUser::create([
            'group_id' => $request->user()->id_group,
            'shortname' => $request->user()->shortname,
            'username' => $username,
            'value' => $value,
            'profile' => $request->profile,
            'ip_address' => $request->ip_address,
            'nas' => $request->nas,
            'member_name' => $request->full_name,
            'kode_area' => $request->kode_area,
            'kode_odp' => $request->kode_odp,
            'type' => $isDhcp ? 'dhcp' : 'pppoe',
            'lock_mac' => $request->lock_mac,
            'mac' => $request->lock_mac_address,
            'status' => $status,
        ]);
    }
}

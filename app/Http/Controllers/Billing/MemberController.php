<?php

namespace App\Http\Controllers\Billing;

use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
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
use Illuminate\Support\Facades\Process;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $members = Member::query()
                ->where('group_id', $request->user()->id_group)
                ->with('ppp:id,username,status', 'invoice');
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

        $total = Member::where('group_id', $request->user()->id_group)->count();
        $totalActive = Member::where('group_id', $request->user()->id_group)
            ->whereHas('ppp', function ($query) {
                $query->where('status', 1);
            })
            ->count();
        $totalSuspend = Member::where('group_id', $request->user()->id_group)
            ->whereHas('ppp', function ($query) {
                $query->where('status', 2);
            })
            ->count();

        return view('billing.members.index', [
            'total' => $total,
            'totalActive' => $totalActive,
            'totalSuspend' => $totalSuspend,
        ]);
    }

    public function getPpp(Request $request)
    {
        $data['ppp'] = PppoeUser::where('id', $request->pppoe_id)->get(['username', 'value', 'profile', 'status', 'created_at']);
        $data['session'] = RadiusSession::where('shortname', $request->user()->shortname)
            ->where('username', $request->pppoe_username)
            ->orderBy('id', 'desc')
            ->first();
        return response()->json($data);
    }

    public function getPayment(Request $request)
    {
        $data = Member::with('profile')
            ->where('id', $request->member_id)
            ->get();
        return response()->json($data);
    }

    public function getContact(Request $request)
    {
        $data = Member::with('ppp')
            ->where('id', $request->member_id)
            ->get();
        return response()->json($data);
    }

    public function getInvoice(Request $request)
    {
        $data = Invoice::with('member')
            ->where('id', $request->invoice_id)
            ->first();
        $ppp = PppoeUser::where('id', $request->ppp_id)
            ->select('id', 'username', 'value', 'nas', 'status', 'profile')
            ->first();
        return response()->json(['data' => $data, 'ppp' => $ppp]);
    }

    public function getListInvoice(Request $request)
    {
        $data = Invoice::where('member_id', $request->member_id)
            ->orderBy('id', 'desc')
            ->with('member:id,full_name')
            ->get();
        return response()->json($data);
    }

    public function updateInvoice(Request $request, Invoice $invoice)
    {
        $amount = str_replace('.', '', $request->amount);
        $invoice->update([
            'item' => $request->item,
            'price' => $amount,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Invoice Berhasil Diupdate',
            'data' => $invoice,
        ]);
    }

    public function updatePayment(Request $request, Member $member)
    {
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
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->next_due)
                ->startOfMonth()
                ->toDateString();
            $member->update([
                'next_due' => $request->next_due,
                'next_invoice' => $next_invoice,
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

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Pembayaran Berhasil Diupdate',
            'data' => $member,
        ]);
    }

    public function updateContact(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $data = [
            'full_name' => $request->full_name,
            'email' => !empty($request->email) ? $request->email : '',
            'wa' => $request->wa,
            'address' => $request->address,
        ];

        if ($request->id_member !== null) {
            $data['id_member'] = $request->id_member;
        }

        $member->update($data);

        $pppoe_user = PppoeUser::where('id', $request->pppoe_id);
        $pppoe_user->update([
            'member_name' => $request->full_name,
        ]);

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Update Billing Contact: ' . $request->full_name . '');

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Kontak Berhasil Diupdate',
            'data' => $member,
        ]);
    }
}

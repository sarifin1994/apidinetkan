<?php

namespace App\Http\Controllers\Billing;

use App\Models\Member;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\Transaksi;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\PppoeProfile;
use Illuminate\Http\Request;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Enums\TransactionCategoryEnum;
use Illuminate\Support\Facades\Process;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UnpaidController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::where('invoice.group_id', $request->user()->id_group)
                ->where('status', 0)
                ->with([
                    'member:id,full_name,wa,kode_area,address',
                    'service.pppoe',
                    'service.profile'
                ]);

            return DataTables::of($invoices)
                ->addIndexColumn()
                ->editColumn('price', function ($row) {
                    $amount_ppn = ($row->price * $row->ppn) / 100;
                    $amount_discount = ($row->price * $row->discount) / 100;

                    if ($row->discount === null) {
                        // No discount
                        return $row->price + $amount_ppn;
                    } elseif ($row->ppn === null) {
                        // No PPN
                        return $row->price - $amount_discount;
                    } else {
                        // Both PPN and discount
                        return $row->price + $amount_ppn - $amount_discount;
                    }
                })
                ->addColumn('action', function ($row) {
                    $url = config('app.url');

                    // Prepare the WhatsApp number
                    $nowa = $row->member->wa ?? '081222339257';
                    $wa = '';
                    if (!preg_match('/[^+0-9]/', trim($nowa))) {
                        if (substr(trim($nowa), 0, 2) == '62') {
                            $wa = trim($nowa);
                        } elseif (substr(trim($nowa), 0, 1) == '0') {
                            $wa = '62' . substr(trim($nowa), 1);
                        }
                    }

                    // Return full HTML, wrapping no_invoice in a badge:
                    return '
                        <a href="javascript:void(0)" id="pay"
                           data-id="' . $row->id . '"
                           data-ppp="' . $row->service->pppoe_id . '"
                           class="badge b-ln-height badge-primary">
                           CONFIRM
                        </a>
                        <a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="' . route('invoice.pay', $row->no_invoice) . '" target="_blank" class="badge b-ln-height badge-warning">
                            <i class="fas fa-bank"></i>
                        </a>
                        <a href="/invoice/pdf/' . $row->no_invoice . '" target="_blank" id="print" class="badge b-ln-height badge-secondary">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="javascript:void(0)"
                           class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    ';
                })
                ->toJson();
        }

        // Non-AJAX part: just return the view
        $members = Member::where('group_id', $request->user()->id_group)
            ->select('id', 'full_name')
            ->get();

        return view('billing.unpaid.index', compact('members'));
    }

    public function getServicesByMember(Request $request, string $memberId)
    {
        $services = PppoeMember::where('member_id', $memberId)
            ->whereHas('member', function ($query) use ($request) {
                $query->where('group_id', $request->user()->id_group);
            })
            ->with(['pppoe:id,username', 'profile:id,name,price'])
            ->get();

        return response()->json($services);
    }

    public function getServiceDetails(Request $request, string $serviceId)
    {
        $service = PppoeMember::where('id', $serviceId)
            ->whereHas('member', function ($query) use ($request) {
                $query->where('group_id', $request->user()->id_group);
            })
            ->with(['pppoe:id,username,value,profile', 'profile:id,name,price', 'invoices'])
            ->firstOrFail();

        return response()->json($service);
    }

    public function getProfile(Request $request)
    {
        $profile = PppoeProfile::where('group_id', $request->user()->id_group)
            ->where('id', $request->profile_id)
            ->get(['id', 'name', 'price']);
        return response()->json($profile);
    }

    public function show() {}

    public function generateInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item' => 'required',
            'amount' => 'required',
            'pppoe_member_id' => 'required|exists:frradius.pppoe_member,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $pppoeMember = PppoeMember::findOrFail($request->pppoe_member_id);
        $member_id = $pppoeMember->member_id;

        $price = str_replace('.', '', $request->amount);
        $ppn = $request->ppn;
        $discount = $request->discount;
        $due_date = $request->next_due;
        $invoice_date = $request->today;
        $payment_type = $request->payment_type;
        $billing_period = $request->billing_period;
        $subscribe = $request->subscribe;
        $full_name = $request->full_name;

        $amount_ppn = ($price * $ppn) / 100;
        $amount_discount = ($price * $discount) / 100;
        $total = $price + $amount_ppn - $amount_discount;

        // Determine period and next_invoice
        if ($payment_type === 'Prabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date);
            $get_periode = date('Y-m-d', strtotime($periode));
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Billing Cycle') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)
                ->startOfMonth()
                ->addMonthsWithNoOverflow(1)
                ->toDateString();
        }

        // Example of generating a custom invoice number
        // $no_invoice = 'INV-'.date('ymd').'-'.rand(1000, 9999);
        // Or keep your original format:
        $no_invoice = date('m') . rand(0000000, 9999999);

        $invoice = Invoice::create([
            'group_id' => $request->user()->id_group,
            'pppoe_id' => $pppoeMember->pppoe_id,
            'member_id' => $member_id,
            'pppoe_member_id' => $pppoeMember->id,
            'no_invoice' => $no_invoice,
            'item' => $request->item,
            'price' => $price,
            'ppn' => $ppn,
            'discount' => $discount,
            'invoice_date' => $invoice_date,
            'due_date' => $due_date,
            'period' => $periode ?? null,
            'subscribe' => $subscribe,
            'payment_type' => $payment_type,
            'billing_period' => $billing_period,
            'payment_url' => route('invoice.pay', $no_invoice),
            'status' => 0,
        ]);

        $pppoeMember->update([
            'next_invoice' => $next_invoice,
        ]);

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Create')
            ->log('Create Manual Invoice: ' . $invoice->no_invoice . ' for ' . $full_name);

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibuat',
            'data' => $invoice,
        ]);
    }

    public function generateInvoiceWA(Request $request, Invoice $invoice_id)
    {
        // Load invoice with necessary relationships
        $invoice = Invoice::with(['service.member', 'service.pppoe', 'service.profile'])
            ->where('id', $invoice_id->id)
            ->firstOrFail();

        // Extract data
        $member = $invoice->service->member;
        $pppoeUser = $invoice->service->pppoe;
        $profile = $invoice->service->profile;

        $full_name = $member->full_name;
        $id_member = $member->id_member;
        $wa = $member->wa;
        $username = $pppoeUser->username;
        $password = $pppoeUser->value;
        $profile_name = $profile->name;
        $payment_type = $invoice->payment_type;
        $billing_period = $invoice->billing_period;
        $item = $invoice->item;
        $price = $invoice->price;
        $ppn = $invoice->ppn;
        $discount = $invoice->discount;
        $due_date = $invoice->due_date;
        $subscribe = $invoice->subscribe;
        $invoice_date = $invoice->invoice_date;

        // Compute total
        $amount_ppn = ($price * $ppn) / 100;
        $amount_discount = ($price * $discount) / 100;
        $total = $price + $amount_ppn - $amount_discount;

        // Format amounts and dates
        $amount_format = number_format($price, 0, '.', '.');
        $total_format = number_format($total, 0, '.', '.');
        $invoice_date_format = date('d/m/Y', strtotime($invoice_date));
        $due_date_format = date('d/m/Y', strtotime($due_date));

        // Compute periode / next_invoice
        if ($payment_type === 'Prabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = indonesiaDateFormat($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Fixed Date') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = indonesiaDateFormat($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)->addMonthsWithNoOverflow(1)->toDateString();
        } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Billing Cycle') {
            $periode = Carbon::createFromFormat('Y-m-d', $due_date)->subMonthsWithNoOverflow(1);
            $get_periode = date('Y-m-d', strtotime($periode));
            $periode_format = indonesiaDateFormat($get_periode);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $due_date)
                ->startOfMonth()
                ->addMonthsWithNoOverflow(1)
                ->toDateString();
        }

        $billing = BillingSetting::where('group_id', $request->user()->id_group)
            ->select('notif_it')
            ->first();

        $no_invoice = $invoice->no_invoice;
        $payment_url = $invoice->payment_url;

        if ($billing->notif_it === 1) {
            if ($wa !== null) {
                $template = WablasTemplate::where('group_id', $request->user()->id_group)
                    ->select('invoice_terbit')
                    ->first()->invoice_terbit;

                $shortcode = [
                    '[nama_lengkap]',
                    '[id_pelanggan]',
                    '[username]',
                    '[password]',
                    '[paket_internet]',
                    '[no_invoice]',
                    '[tgl_invoice]',
                    '[jumlah]',
                    '[ppn]',
                    '[discount]',
                    '[total]',
                    '[periode]',
                    '[jth_tempo]',
                    '[payment_midtrans]'
                ];
                $source = [
                    $full_name,
                    $id_member,
                    $username,
                    $password,
                    $profile_name,
                    $no_invoice,
                    $invoice_date_format,
                    $amount_format,
                    $ppn,
                    $discount,
                    $total_format,
                    $periode_format,
                    $due_date_format,
                    $payment_url
                ];
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                $curl = curl_init();
                $wablas = Wablas::where('group_id', $request->user()->id_group)
                    ->select('token', 'sender')
                    ->first();
                $data = [
                    'api_key' => $wablas->token,
                    'sender' => $wablas->sender,
                    'number' => $wa,
                    'message' => $message_format,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response, true);

                $pesan = [];
                foreach ($result['data'] as $row) {
                    $draw = [
                        'group_id' => $request->user()->id_group,
                        'id_message' => $row['note'],
                        'subject' => 'INVOICE TERBIT #' . $no_invoice,
                        'message' => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                        'phone' => $row['number'],
                        'status' => $row['status'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    $pesan[] = $draw;
                }
                WablasMessage::insert($pesan);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'WA Berhasil Terkirim',
        ]);
    }

    public function printInvoice(Request $request)
    {
        $no_invoice = last(request()->segments());
        $invoice = Invoice::where('group_id', $request->user()->id_group)
            ->where('no_invoice', $no_invoice)
            ->with('member')
            ->get();

        $pdf = Pdf::loadView('billing.invoice.print_thermal', compact('invoice'));

        return $pdf->stream();
    }

    public function payInvoice(Request $request, Invoice $invoice)
    {
        $group_id = $request->user()->id_group;

        if ($request->payment_type === 'Prabayar') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date') {
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)->addMonthsWithNoOverflow(1);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle') {
            $due_bc = BillingSetting::where('group_id', $group_id)->select('due_bc')->first();
            $next_due = Carbon::createFromFormat('Y-m-d', $request->due_date)
                ->setDay($due_bc->due_bc)
                ->addMonths(1);
            $next_invoice = Carbon::createFromFormat('Y-m-d', $request->due_date)
                ->startOfMonth()
                ->addMonths(1);
        }

        $transaksi = Transaksi::create([
            'group_id' => $group_id,
            'invoice_id' => $invoice->id,
            'invoice_type' => Invoice::class,
            'type' => TransactionTypeEnum::INCOME,
            'category' => TransactionCategoryEnum::INVOICE,
            'item' => 'Invoice',
            'deskripsi' => "Payment #$request->no_invoice a.n $request->full_name",
            'price' => $request->payment_total,
            'tanggal' => Carbon::now(),
            'payment_method' => $request->payment_method,
            'admin' => $request->user()->username,
        ]);

        $invoice->update([
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'paid_date' => Carbon::today()->toDateString(),
            'status' => 1,
        ]);

        $cek_inv = Invoice::where([['member_id', $request->member_id], ['status', 0]])->count();
        if ($request->ppp_status === '2' && $cek_inv === 0) {
            $ppp = PppoeUser::where('id', $request->ppp_id);
            $ppp->update([
                'status' => 1,
            ]);
            if ($request->nas === null) {
                $nas = RadiusNas::where('group_id', $group_id)->select('nasname', 'secret')->get();
                foreach ($nas as $nasitem) {
                    $command = Process::path('/usr/bin/')->run("echo User-Name='$request->pppoe_user' | radclient -r 1 $nasitem[nasname]:3799 disconnect $nasitem[secret]");
                }
            } else {
                $nas_secret = RadiusNas::where('group_id', $group_id)
                    ->where('nasname', $request->nas)
                    ->select('secret')
                    ->first();
                $command = Process::path('/usr/bin/')->run("echo User-Name='$request->pppoe_user' | radclient -r 1 $request->nas:3799 disconnect $nas_secret->secret");
            }
        }

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Pay Invoice: ' . $request->no_invoice . ' a.n ' . $request->full_name . '');

        return response()->json([
            'success' => true,
            'message' => 'Invoice Berhasil Dibayar',
        ]);
    }

    public function payInvoiceWA(Request $request, Invoice $invoice)
    {
        $group_id = $request->user()->id_group;
        $billing = BillingSetting::where('group_id', $group_id)->select('notif_ps')->first();
        if ($billing->notif_ps === 1) {
            $wa = $request->no_wa;
            if ($wa !== null) {
                $get_periode = date('Y-m-d', strtotime($request->periode));
                $periode_format = indonesiaDateFormat($get_periode);
                $amount_format = number_format($request->amount, 0, ',', '.');
                $total_format = number_format($request->payment_total, 0, ',', '.');
                $due_date_format = date('d/m/Y', strtotime($request->due_date));
                $invoice_date_format = date('d/m/Y', strtotime($request->invoice_date));
                if ($request->payment_method === '1') {
                    $payment_method = 'Cash';
                } else {
                    $payment_method = 'Transfer';
                }
                $template = WablasTemplate::where('group_id', $group_id)->select('payment_paid')->first()->payment_paid;
                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[paket_internet]', '[no_invoice]', '[tgl_invoice]', '[jumlah]', '[ppn]', '[discount]', '[total]', '[periode]', '[jth_tempo]', '[metode_pembayaran]', '[payment_midtrans]'];
                $source = [$request->full_name, $request->id_member, $request->pppoe_user, $request->pppoe_pass, $request->pppoe_profile, $request->no_invoice, $invoice_date_format, $amount_format, $request->ppn, $request->discount, $total_format, $periode_format, $due_date_format, $payment_method, $request->payment_url];
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                $curl = curl_init();
                $wablas = Wablas::where('group_id', $group_id)->select('token', 'sender')->first();
                $data = [
                    'api_key' => $wablas->token,
                    'sender' => $wablas->sender,
                    'number' => $wa,
                    'message' => $message_format,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response, true);
                $pesan = [];
                foreach ($result['data'] as $row) {
                    $draw = [
                        'group_id' => $group_id,
                        'id_message' => $row['note'],
                        'subject' => 'INVOICE PAID #' . $invoice->no_invoice,
                        'message' => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                        'phone' => $row['number'],
                        'status' => $row['status'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    $pesan[] = $draw;
                }
                $save = WablasMessage::insert($pesan);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'WA Berhasil Terkirim',
        ]);
    }

    public function destroy($id)
    {
        $unpaid = Invoice::findOrFail($id);
        $unpaid->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}

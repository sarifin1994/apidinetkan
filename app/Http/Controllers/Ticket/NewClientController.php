<?php

namespace App\Http\Controllers\Ticket;

use App\Enums\NewClientTicketStatusEnum;
use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BillingSetting;
use App\Models\Invoice;
use App\Models\License;
use App\Models\Member;
use App\Models\Nas;
use App\Models\PppoeMember;
use App\Models\PppoeProfile;
use App\Models\PppoeUser;
use App\Models\TelegramBot;
use App\Models\TicketPsb;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Wablas;
use App\Models\WablasMessage;
use App\Models\WablasTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Data\Services\MemberService;
use Modules\Data\Services\PppoeService;
use Spatie\Activitylog\Models\Activity;
use Telegram\Bot\Laravel\Facades\Telegram;
use Yajra\DataTables\Facades\DataTables;

class NewClientController extends Controller
{
    public function __construct(
        protected MemberService $dataService,
        protected PppoeService $pppoeService
    ) {}

    public function index(Request $request)
    {
        $areas = Area::where('group_id', $request->user()->id_group)
            ->select('id', 'kode_area')
            ->orderBy('kode_area', 'desc')
            ->get();

        $nas = Nas::where('group_id', $request->user()->id_group)
            ->select('ip_router', 'name')
            ->get();

        $profiles = PppoeProfile::where('group_id', $request->user()->id_group)
            ->select('id', 'name', 'price')
            ->orderBy('name', 'desc')
            ->get();

        if ($request->ajax()) {
            return $this->dataTableAjax($request);
        }

        $totalPending = TicketPsb::where('group_id', $request->user()->id_group)->where('status', NewClientTicketStatusEnum::PENDING)->count();
        $totalOpen = TicketPsb::where('group_id', $request->user()->id_group)->where('status', NewClientTicketStatusEnum::OPEN)->count();
        $totalClosed = TicketPsb::where('group_id', $request->user()->id_group)->where('status', NewClientTicketStatusEnum::CLOSED)->count();

        $members = Member::where('group_id', $request->user()->id_group)
            ->select('id', 'full_name')
            ->get();

        return view('tickets.new.index', compact(
            'areas',
            'nas',
            'profiles',
            'totalPending',
            'totalOpen',
            'totalClosed',
            'members'
        ));
    }

    public function show(TicketPsb $new)
    {
        $new->load('rmember');

        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $new,
        ]);
    }

    public function confirmPsb(Request $request, TicketPsb $psb)
    {
        $psb->update([
            'status' => NewClientTicketStatusEnum::CLOSED,
            'tgl_aktif' => Carbon::today()->toDateString(),
            'note' => $request->note,
            'closed_by' => $request->user()->name,
        ]);

        $this->sendTelegramPsbInstalled($request, $psb);
        $this->sendWhatsAppAccountActive($request, $psb);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
        ]);
    }

    public function storeSecret(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('db_radius.user_pppoe')->where('group_id', $request->user()->id_group)
            ],
            'password' => 'required|string',
            'profile' => 'required',
            'reg_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        // License check
        if (!$this->checkLicense($request)) {
            return response()->json([
                'error' => 'Sorry your license is limited, please upgrade!',
            ]);
        }

        $psb = TicketPsb::where('id', $request->id)->first();
        $member = Member::findOrFail($psb->member_id);

        // Validate reg_date based on conditions
        if (!$this->validateRegDate($request)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid reg_date.'
            ]);
        }

        if (!$request->has('member_id')) {
            $request->request->add(['member_id' => $member->id]);
        }

        $kode_area = Area::where('id', $request->kode_area_add)->value('kode_area') ?? null;
        $user = null;

        try {
            // Create PPPoE User
            $user = PppoeUser::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'member_name' => $member->full_name,
                'kode_area' => $kode_area,
                'kode_odp' => $request->kode_odp_add,
                'lock_mac' => $request->lock_mac,
                'mac' => $request->mac,
                'status' => 1,
            ]);

            // Create PPPoE Member
            $service = $this->pppoeService->handleMemberUserCreation($request, $user);

            if (!$service instanceof PppoeMember) {
                return $service;
            }
        } catch (\Exception $e) {
            if ($user) {
                $user->delete();
            }

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }

        // Update PSB
        $psb->update([
            'pppoe_id' => $user->id,
            'status' => NewClientTicketStatusEnum::OPEN,
            'note' => $request->note,
        ]);

        $this->sendTelegramNewPsb($request, $member);

        return response()->json([
            'success' => true,
            'message' => 'Data PSB Berhasil Disimpan',
        ]);
    }

    public function store(Request $request)
    {
        $paket = explode('|', $request->paket);
        $paket_id = $paket[0];
        $paket_name = $paket[1];

        $member = Member::create([
            'group_id' => $request->user()->id_group,
            'id_member' => $this->dataService->generateMemberId($request->user()),
            'profile_id' => $paket_id,
            'full_name' => $request->nama_lengkap,
            'wa' => $request->no_wa,
            'address' => $request->alamat,
        ]);

        $ticket = TicketPsb::create([
            'group_id' => $request->user()->id_group,
            'id_psb' => 'PSB' . date('m') . rand(000, 999),
            'paket' => $paket_name,
            'paket_id' => $paket_id,
            'member_id' => $member->id,
            'status' => NewClientTicketStatusEnum::PENDING,
            'tgl_psb' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->sendTelegramPsbRequest($request, $paket_name);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
        ]);
    }

    public function destroy($id)
    {
        $psb = TicketPsb::findOrFail($id);
        $psb->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function getService(Request $request, PppoeUser $user)
    {
        $service = $user->load('member');
        return response()->json($service);
    }

    public function getMemberDetails(Request $request, Member $member)
    {
        return response()->json($member);
    }

    // PRIVATE METHODS

    private function dataTableAjax(Request $request)
    {
        $psbQuery = TicketPsb::where('group_id', $request->user()->id_group)
            ->with('rpppoe', 'rmember');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        if (!empty($start_date) && !empty($end_date)) {
            $psbQuery->whereBetween('tgl_psb', [$start_date, $end_date]);
        }

        return DataTables::of($psbQuery)
            ->addIndexColumn()
            ->editColumn('full_name', function ($psb) {
                return $psb->rmember->full_name;
            })
            ->addColumn('action', function ($row) use ($request) {
                return $this->renderActionButtons($row, $request);
            })
            ->toJson();
    }

    private function renderActionButtons($row, $request)
    {
        $wa = $this->formatWaNumber($row->no_wa);

        if ($row->status === NewClientTicketStatusEnum::OPEN) {
            return '<a href="javascript:void(0)" id="confirm"
                data-id="' . $row->id . '" class="badge b-ln-height badge-primary">
                <i class="fas fa-check"></i>
            </a>
            <a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="javascript:void(0)" class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                <i class="fas fa-trash-alt"></i>
            </a>';
        } elseif ($row->status === NewClientTicketStatusEnum::PENDING) {
            if ($request->user()->role === 'Admin' || $request->user()->role === 'Helpdesk') {
                return '<a href="javascript:void(0)" id="create_secret"
                    data-id="' . $row->id . '" class="badge b-ln-height badge-warning">
                    <i class="fas fa-plus"></i>
                </a>
                <a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="javascript:void(0)" class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                    <i class="fas fa-trash-alt"></i>
                </a>';
            } else {
                return '<a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="javascript:void(0)" class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash-alt"></i>
                        </a>';
            }
        } else {
            return '<a href="https://wa.me/' . $wa . '" target="_blank" class="badge b-ln-height badge-success">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="javascript:void(0)" class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                        <i class="fas fa-trash-alt"></i>
                    </a>';
        }
    }

    private function formatWaNumber($nowa)
    {
        $wa = '';
        if (!preg_match('/[^+0-9]/', trim($nowa))) {
            if (substr(trim($nowa), 0, 2) == '62') {
                $wa = trim($nowa);
            } elseif (substr(trim($nowa), 0, 1) == '0') {
                $wa = '62' . substr(trim($nowa), 1);
            }
        }
        return $wa;
    }

    private function sendTelegramPsbInstalled(Request $request, TicketPsb $psb)
    {
        $telegram_psb = TelegramBot::where('group_id', $request->user()->id_group)
            ->where('tipe', 1)
            ->first();
        $psb_chatid = $telegram_psb->chatid;

        $today = Carbon::now()->format('d/m/Y H:i');
        $text = "<b>✅ DATA PSB TERPASANG</b>\n========================\nTanggal: <b>$today</b>\n========================\nNama Lengkap: <b>$request->full_name</b>\nAlamat: <b>$request->alamat</b>\n========================\nStatus: <b>CLOSED / SUDAH TERPASANG</b>\nClosed By: <b>{$request->user()->name}</b>";

        Telegram::sendMessage([
            'chat_id' => $psb_chatid,
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);
    }

    private function sendWhatsAppAccountActive(Request $request, TicketPsb $psb)
    {
        $billing = BillingSetting::where('group_id', $request->user()->id_group)
            ->select('notif_sm')
            ->first();

        if (!$billing || $billing->notif_sm !== 1) {
            return;
        }

        $member = Member::where('id', $request->member_id)
            ->with('services.profile', 'services.pppoe')
            ->first();

        if (!$member || !$member->wa) {
            return;
        }

        $service = $member->services->first();
        if (!$service) {
            return;
        }

        $harga_format = number_format($service->profile->price, 0, '.', '.');
        $due_date_format = date('d/m/Y', strtotime($service->next_due));

        $template = WablasTemplate::where('group_id', $request->user()->id_group)
            ->select('account_active')
            ->first()->account_active;

        $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[alamat]', '[username]', '[password]', '[paket_internet]', '[harga]', '[tipe_pembayaran]', '[siklus_tagihan]', '[tgl_aktif]', '[jth_tempo]'];
        $source = [
            $member->full_name,
            $member->id_member,
            $member->address,
            $service->pppoe->username,
            $service->pppoe->value,
            $service->profile->name,
            $harga_format,
            $service->payment_type,
            $service->billing_period,
            $service->reg_date,
            $due_date_format
        ];

        $message = str_replace($shortcode, $source, $template);
        $message_format = str_replace('<br>', "\n", $message);

        $wablas = Wablas::where('group_id', $request->user()->id_group)
            ->select('token', 'sender')
            ->first();

        $data = [
            'api_key' => $wablas->token,
            'sender' => $wablas->sender,
            'number' => $member->wa,
            'message' => $message_format,
        ];

        $response = $this->sendWablasMessage($data);
        $this->saveWablasMessage($request, $response, 'PELANGGAN AKTIF #' . $member->id_member, $message);
    }

    private function sendTelegramPsbRequest(Request $request, $paket_name)
    {
        $telegram_psb = TelegramBot::where('group_id', $request->user()->id_group)
            ->where('tipe', 1)
            ->first();
        $psb_chatid = $telegram_psb->chatid;

        $today_telegram = Carbon::now()->format('d/m/Y H:i');
        $text = "<b>⭕️ REQUEST PSB BARU</b>\n========================\nTanggal: <b>$today_telegram</b>\n========================\nNama Lengkap: <b>{$request->nama_lengkap}</b>\nNomor WA: <b>{$request->no_wa}</b>\nAlamat: <b>{$request->alamat}</b>\n========================\nPaket Internet: <b>$paket_name\n</b>========================\nInput By: <b>{$request->user()->name}</b>";

        Telegram::sendMessage([
            'chat_id' => $psb_chatid,
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);
    }

    private function sendTelegramNewPsb(Request $request, Member $member)
    {
        $telegram_psb = TelegramBot::where('group_id', $request->user()->id_group)
            ->where('tipe', 1)
            ->first();
        $psb_chatid = $telegram_psb->chatid;
        $today_telegram = Carbon::now()->format('d/m/Y H:i');

        $text = "<b>⭕️ DATA PSB BARU</b>\n========================\nTanggal: <b>$today_telegram</b>\n========================\nID Pelanggan: <b>$member->id_member</b>\nNama Lengkap: <b>{$member->full_name}</b>\nNomor WA: <b>{$member->wa}</b>\nAlamat: <b>{$member->address}</b>\n========================\nUsername: <b>{$request->username}</b>\nPassword: <b>{$request->password}</b>\nPaket Internet: <b>{$request->profile}\n</b>Kode ODP: <b>{$request->kode_odp_add}\n</b>========================\nStatus: <b>OPEN / BELUM TERPASANG</b>\n";

        Telegram::sendMessage([
            'chat_id' => $psb_chatid,
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);
    }

    private function sendWablasMessage($data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    private function saveWablasMessage(Request $request, $result, $subject, $message)
    {
        if (!isset($result['data'])) {
            return;
        }

        $pesan = [];
        foreach ($result['data'] as $row) {
            $pesan[] = [
                'group_id' => $request->user()->id_group,
                'id_message' => $row['note'],
                'subject' => $subject,
                'message' => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                'phone' => $row['number'],
                'status' => $row['status'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        WablasMessage::insert($pesan);
    }

    private function checkLicense(Request $request)
    {
        $admin = User::where('id_group', $request->user()->id_group)
            ->where('role', 'Admin')
            ->first();
        $pppoeCount = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $limit = License::where('id', $admin->license_id)
            ->select('limit_pppoe')
            ->first()->limit_pppoe;

        return $pppoeCount < $limit;
    }

    private function validateRegDate(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $cek_reg_date = Carbon::createFromFormat('Y-m-d', $today)->subMonthsWithNoOverflow(2)->toDateString();
        $cek_reg_date_bc = Carbon::createFromFormat('Y-m-d', $today)->startOfMonth()->subMonthsWithNoOverflow(2)->toDateString();

        $payment_type = $request->payment_type;
        $billing_period = $request->billing_period;
        $reg_date = $request->reg_date;

        if ($reg_date < $cek_reg_date) {
            return false;
        }

        return true;
    }
}

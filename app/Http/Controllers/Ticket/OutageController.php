<?php

namespace App\Http\Controllers\Ticket;

use App\Models\Area;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\TicketGgn;
use App\Models\TelegramBot;
use Illuminate\Http\Request;
use App\Models\RadiusSession;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Laravel\Facades\Telegram;

class OutageController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::where('group_id', $request->user()->id_group)->select('id', 'kode_area')->orderBy('kode_area', 'desc')->get();
        $members = Member::where('group_id', $request->user()->id_group)->select('full_name', 'kode_area', 'id')->get();
        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $ggn = TicketGgn::query()->where('group_id', $request->user()->id_group)
                    ->with('rpppoe', 'rmember')
                    ->whereBetween('tgl_open', [Carbon::parse($start_date)->format('Y-m-d 00:00:00'), Carbon::parse($end_date)->format('Y-m-d 23:59:59')]);
            } else {
                $ggn = TicketGgn::query()->where('group_id', $request->user()->id_group)->with('rpppoe', 'rmember');
            }
            return DataTables::of($ggn)
                ->addIndexColumn()
                ->editColumn('full_name', function ($ggn) {
                    return $ggn->rmember->full_name;
                })
                ->addColumn('action', function ($row) {
                    if ($row->rmember->wa === null) {
                        $nowa = '081222339257';
                    } else {
                        $nowa = $row->rmember->wa;
                    }
                    if (!preg_match('/[^+0-9]/', trim($nowa))) {
                        // cek apakah no hp karakter ke 1 dan 2 adalah angka 62
                        if (substr(trim($nowa), 0, 2) == '62') {
                            $wa = trim($nowa);
                        }
                        // cek apakah no hp karakter ke 1 adalah angka 0
                        elseif (substr(trim($nowa), 0, 1) == '0') {
                            $wa = '62' . substr(trim($nowa), 1);
                        } else {
                            $wa = '';
                        }
                    }
                    if ($row->status === 0) {
                        return '<a href="javascript:void(0)" id="confirm"
                        data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-primary">
                            <i class="fas fa-check"></i>
                    </a>
                <a href="https://wa.me/' .
                            $wa .
                            '" target="_blank" class="badge b-ln-height badge-success">
            <i class="fab fa-whatsapp"></i>
            </a>

            <a href="javascript:void(0)"
            class="badge b-ln-height badge-danger" id="delete" data-id="' .
                            $row->id .
                            '">
            <i class="fas fa-trash-alt"></i>
            </a>';
                    } else {
                        return '
                <a href="https://wa.me/' .
                            $wa .
                            '" target="_blank" class="badge b-ln-height badge-success">
                        <i class="fab fa-whatsapp"></i>
                </a>
                <a href="javascript:void(0)"
                class="badge b-ln-height badge-danger" id="delete" data-id="' .
                            $row->id .
                            '">
                <i class="fas fa-trash-alt"></i>
                </a>';
                    }
                })

                ->toJson();
        }

        $totalOpen = TicketGgn::where('group_id', $request->user()->id_group)->where('status', 0)->count();
        $totalClosed = TicketGgn::where('group_id', $request->user()->id_group)->where('status', 1)->count();

        return view('tickets.outage.index', compact('areas', 'members', 'totalOpen', 'totalClosed'));
    }

    public function show(TicketGgn $outage)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $outage,
        ]);
    }

    public function store(Request $request)
    {
        $telegram_ggn = TelegramBot::where('group_id', $request->user()->id_group)->where('tipe', 2)->get()->first();
        $ggn_chatid = $telegram_ggn->chatid;
        if ($request->tipe === '1') {
            $validator = Validator::make($request->all(), [
                'member_id' => 'required',
                'jenis' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                ]);
            }
            $ggn = TicketGgn::create([
                'group_id' => $request->user()->id_group,
                'id_ggn' => 'GGN' . date('m') . rand(00000, 99999),
                'tipe' => $request->tipe,
                'member_id' => $request->member_id,
                'pppoe_id' => $request->pppoe_id,
                'nama_lengkap' => $request->nama_lengkap,
                'kode_area' => $request->area,
                'jenis' => $request->jenis,
                'note' => $request->note,
                'status' => 0,
                'tgl_open' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            $today = Carbon::now()->format('d/m/Y H:i');
            $text = "<b>⭕️ DATA GGN INDIVIDU</b>\n" . "========================\n" . "Tanggal: <b>$today</b>\n" . "========================\n" . "Nama Pelanggan: <b>$request->nama_lengkap</b>\n" . "Nomor WA: <b>$request->wa</b>\n" . "Alamat: <b>$request->alamat</b>\n" . "Kode ODP: <b>$request->odp</b>\n" . "========================\n" . "Jenis Gangguan: <b>$request->jenis\n</b>" . "Status Internet: <b>$request->internet\n</b>" . "IP Address: <b>$request->ip\n</b>" . "Note: <b>$request->note</b>\n" . "========================\n" . "Status: <b>OPEN</b>\n";

            $chat = Telegram::sendMessage([
                'chat_id' => $ggn_chatid,
                'parse_mode' => 'HTML',
                'text' => $text,
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'kode_area' => 'required',
                'jenis_massal' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                ]);
            }
            $ggn = TicketGgn::create([
                'group_id' => $request->user()->id_group,
                'id_ggn' => 'GGN' . date('m') . rand(00000, 99999),
                'tipe' => $request->tipe,
                'nama_lengkap' => 'MASSAL',
                'kode_area' => $request->kode_area,
                'jenis' => $request->jenis_massal,
                'note' => $request->note_massal,
                'status' => 0,
                'tgl_open' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            $today = Carbon::now()->format('d/m/Y H:i');
            $text = "<b>⭕️ DATA GGN MASSAL</b>\n" . "========================\n" . "Tanggal: <b>$today</b>\n" . "========================\n" . "Kode Area: <b>$request->kode_area</b>\n" . "Jenis Gangguan: <b>$request->jenis_massal\n</b>" . "Note: <b>$request->note_massal</b>\n" . "========================\n" . "Status: <b>OPEN</b>\n";
            $chat = Telegram::sendMessage([
                'chat_id' => $ggn_chatid,
                'parse_mode' => 'HTML',
                'text' => $text,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
        ]);
    }

    public function getArea(Request $request)
    {
        $area = Member::where('id', $request->member_id)
            ->with('ppp:id,username,kode_odp')
            ->get(['id', 'kode_area', 'full_name', 'address', 'pppoe_id', 'wa']);
        return response()->json($area);
    }

    public function getSession(Request $request)
    {
        $sessions = RadiusSession::where('shortname', $request->user()->shortname)->with('ppp:username,status')
            ->where('username', $request->pppoe_username)
            ->orderBy('id', 'desc')
            ->first();
        return response()->json($sessions);
    }

    public function confirmGgn(Request $request, TicketGgn $ggn)
    {
        $telegram_ggn = TelegramBot::where('group_id', $request->user()->id_group)->where('tipe', 2)->get()->first();
        $ggn_chatid = $telegram_ggn->chatid;
        $ggn->update([
            'status' => 1, // closed
            'tgl_closed' => Carbon::now()->format('Y-m-d H:i:s'),
            'penyelesaian' => $request->penyelesaian,
            'closed_by' => $request->user()->name,
        ]);
        if ($request->tipe === '1') {
            $today = Carbon::now()->format('d/m/Y H:i');
            $text = "<b>✅ DATA GGN INDIVIDU</b>\n" . "========================\n" . "Tanggal: <b>$today</b>\n" . "========================\n" . "Nama Pelanggan: <b>$request->nama</b>\n" . "Kode Area: <b>$request->kode_area</b>\n" . "========================\n" . "Jenis Gangguan: <b>$request->jenis\n</b>" . "Penyelesaian: <b>$request->penyelesaian\n</b>" . "========================\n" . "Status: <b>CLOSED</b>\n" . 'Closed By: <b>' . $request->user()->name . '</b>';

            $chat = Telegram::sendMessage([
                'chat_id' => $ggn_chatid,
                'parse_mode' => 'HTML',
                'text' => $text,
            ]);
        } else {
            $today = Carbon::now()->format('d/m/Y H:i');
            $text = "<b>✅ DATA GGN MASSAL</b>\n" . "========================\n" . "Tanggal: <b>$today</b>\n" . "========================\n" . "Kode Area: <b>$request->kode_area</b>\n" . "Jenis Gangguan: <b>$request->jenis\n</b>" . "Penyelesaian: <b>$request->penyelesaian\n</b>" . "========================\n" . "Status: <b>CLOSED</b>\n" . 'Closed By: <b>' . $request->user()->name . '</b>';

            $chat = Telegram::sendMessage([
                'chat_id' => $ggn_chatid,
                'parse_mode' => 'HTML',
                'text' => $text,
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $ggn,
            $chat,
        ]);
    }

    public function destroy($id)
    {
        $ggn = TicketGgn::findOrFail($id);
        $ggn->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}

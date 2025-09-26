<?php

namespace App\Http\Controllers\Tiket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Spatie\Activitylog\Contracts\Activity;
use App\Models\Tiket\TiketGangguan;
use App\Models\Whatsapp\Mpwa;
use App\Models\Pppoe\PppoeUser;
use App\Models\User;
use App\Models\Radius\RadiusSession;
use Illuminate\Support\Facades\Validator;
use App\Models\Whatsapp\Watemplate;
use App\Models\Setting\Company;
use App\Models\Mapping\Pop;

class TiketGangguanController extends Controller
{
    public function index()
    {
        $pelanggan = PppoeUser::where('shortname', multi_auth()->shortname)->get();
        $areas = Pop::where('shortname', multi_auth()->shortname)->get();

        $teknisi = User::where('shortname', multi_auth()->shortname)->where('role', 'Teknisi')->get();

        if (request()->ajax()) {
            $tiket = TiketGangguan::query()->where('shortname', multi_auth()->shortname);
            return DataTables::of($tiket)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
        <a href="javascript:void(0)" id="close"
            data-id="' . $row->id . '"
            data-id_pelanggan="' . $row->pelanggan_id . '"
            data-nomor_tiket="' . $row->nomor_tiket . '"
            class="btn btn-primary text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class=\'ti ti-circle-check me-1\'></i> CLOSE
        </a>
        <a href="javascript:void(0)" id="delete"
            data-id="' . $row->id . '"
            class="btn btn-danger text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class=\'ti ti-trash me-1\'></i> DELETE
        </a>';
                })

                ->toJson();
        }

        return view(
            'backend.tiket.gangguan.index_new',
            compact(
                'pelanggan',
                'teknisi',
                'areas',
            ),
        );
    }

    public function grafikTiket()
    {

        // Dapatkan tahun saat ini
        $year = Carbon::now()->year;

        // Array mapping nomor bulan ke nama bulan dalam bahasa Indonesia
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Buat array untuk key bulan (format "YYYY-MM") dan label bulan (nama Indonesia)
        $monthKeys = [];
        $monthLabels = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthKeys[] = sprintf('%d-%02d', $year, $m);
            $monthLabels[] = $monthNames[$m];
        }

        // Ambil tiket untuk tahun ini sesuai shortname
        $tickets = TiketGangguan::where('shortname', multi_auth()->shortname)->whereYear('created_at', $year)->get();

        // Inisialisasi struktur data untuk masing-masing grafik
        $totalTicketsMonthly = []; // Grafik 1
        $closedTicketsByTechnicianMonthly = []; // Grafik 2 (hanya tiket closed)
        $resolutionTimeByTechnicianMonthly = []; // Grafik 3 (waktu penyelesaian dalam jam)
        $gangguanByJenisMonthly = []; // Grafik 4
        $penyelesaianByMonthly = []; // Grafik 5

        // Proses setiap tiket
        foreach ($tickets as $ticket) {
            $ticketMonth = Carbon::parse($ticket->created_at)->format('Y-m');

            // --- Grafik 1: Total tiket per bulan ---
            if (!isset($totalTicketsMonthly[$ticketMonth])) {
                $totalTicketsMonthly[$ticketMonth] = 0;
            }
            $totalTicketsMonthly[$ticketMonth]++;

            // --- Grafik 2: Total tiket closed per teknisi per bulan ---
            if (!isset($closedTicketsByTechnicianMonthly[$ticketMonth])) {
                $closedTicketsByTechnicianMonthly[$ticketMonth] = [];
            }
            if ($ticket->status == 'closed') {
                $tech = $ticket->teknisi;
                if (!isset($closedTicketsByTechnicianMonthly[$ticketMonth][$tech])) {
                    $closedTicketsByTechnicianMonthly[$ticketMonth][$tech] = 0;
                }
                $closedTicketsByTechnicianMonthly[$ticketMonth][$tech]++;
            }

            // --- Grafik 3: Waktu penyelesaian per teknisi (dalam jam) per bulan ---
            if (!isset($resolutionTimeByTechnicianMonthly[$ticketMonth])) {
                $resolutionTimeByTechnicianMonthly[$ticketMonth] = [];
            }
            if ($ticket->status == 'closed' && $ticket->closed_at) {
                $tech = $ticket->teknisi;
                if (!isset($resolutionTimeByTechnicianMonthly[$ticketMonth][$tech])) {
                    $resolutionTimeByTechnicianMonthly[$ticketMonth][$tech] = ['total' => 0, 'count' => 0];
                }
                // Hitung durasi dalam menit lalu bagi 60 untuk mendapatkan jam, lalu casting ke integer
                $durationHours = (int)(Carbon::parse($ticket->created_at)
                    ->diffInMinutes(Carbon::parse($ticket->closed_at)) / 60);
                $resolutionTimeByTechnicianMonthly[$ticketMonth][$tech]['total'] += $durationHours;
                $resolutionTimeByTechnicianMonthly[$ticketMonth][$tech]['count']++;
            }

            // --- Grafik 4: Jenis gangguan per bulan ---
            if (!isset($gangguanByJenisMonthly[$ticketMonth])) {
                $gangguanByJenisMonthly[$ticketMonth] = [];
            }
            $jenisGangguan = $ticket->jenis_gangguan ? $ticket->jenis_gangguan : 'Lainnya';
            if (!isset($gangguanByJenisMonthly[$ticketMonth][$jenisGangguan])) {
                $gangguanByJenisMonthly[$ticketMonth][$jenisGangguan] = 0;
            }
            $gangguanByJenisMonthly[$ticketMonth][$jenisGangguan]++;

            // --- Grafik 5: Penyelesaian gangguan per bulan ---
            if (!isset($penyelesaianByMonthly[$ticketMonth])) {
                $penyelesaianByMonthly[$ticketMonth] = [];
            }
            $penyelesaian = $ticket->penyelesaian ? $ticket->penyelesaian : 'Lainnya';
            if (!isset($penyelesaianByMonthly[$ticketMonth][$penyelesaian])) {
                $penyelesaianByMonthly[$ticketMonth][$penyelesaian] = 0;
            }
            $penyelesaianByMonthly[$ticketMonth][$penyelesaian]++;
        }

        // --- Persiapan data untuk Grafik 2 dan 3 ---
        // Ambil daftar semua teknisi (dari tiket closed)
        $allTechnicians = [];
        foreach ($closedTicketsByTechnicianMonthly as $month => $techData) {
            foreach ($techData as $tech => $count) {
                if (!in_array($tech, $allTechnicians)) {
                    $allTechnicians[] = $tech;
                }
            }
        }
        sort($allTechnicians);

        // Siapkan data untuk Grafik 2: Closed tiket per teknisi per bulan
        $closedTicketsByTech = [];
        foreach ($allTechnicians as $tech) {
            $closedTicketsByTech[$tech] = [];
            foreach ($monthKeys as $month) {
                $closedTicketsByTech[$tech][] = isset($closedTicketsByTechnicianMonthly[$month][$tech]) ? $closedTicketsByTechnicianMonthly[$month][$tech] : 0;
            }
        }

        // Siapkan data untuk Grafik 3: Rata-rata waktu penyelesaian (jam) per teknisi per bulan
        $avgResolutionTimeByTech = [];
        foreach ($allTechnicians as $tech) {
            $avgResolutionTimeByTech[$tech] = [];
            foreach ($monthKeys as $month) {
                if (isset($resolutionTimeByTechnicianMonthly[$month][$tech])) {
                    $data = $resolutionTimeByTechnicianMonthly[$month][$tech];
                    $avg = $data['count'] > 0 ? $data['total'] / $data['count'] : 0;
                } else {
                    $avg = 0;
                }
                $avgResolutionTimeByTech[$tech][] = $avg;
            }
        }

        // --- Persiapan data untuk Grafik 4: Jenis gangguan per bulan ---
        $allJenisGangguan = [];
        foreach ($gangguanByJenisMonthly as $month => $data) {
            foreach ($data as $jenis => $count) {
                if (!in_array($jenis, $allJenisGangguan)) {
                    $allJenisGangguan[] = $jenis;
                }
            }
        }
        sort($allJenisGangguan);
        $gangguanByJenisData = [];
        foreach ($allJenisGangguan as $jenis) {
            $gangguanByJenisData[$jenis] = [];
            foreach ($monthKeys as $month) {
                $gangguanByJenisData[$jenis][] = isset($gangguanByJenisMonthly[$month][$jenis]) ? $gangguanByJenisMonthly[$month][$jenis] : 0;
            }
        }

        // --- Persiapan data untuk Grafik 5: Penyelesaian per bulan ---
        $allPenyelesaian = [];
        foreach ($penyelesaianByMonthly as $month => $data) {
            foreach ($data as $peny => $count) {
                if (!in_array($peny, $allPenyelesaian)) {
                    $allPenyelesaian[] = $peny;
                }
            }
        }
        sort($allPenyelesaian);
        $penyelesaianData = [];
        foreach ($allPenyelesaian as $peny) {
            $penyelesaianData[$peny] = [];
            foreach ($monthKeys as $month) {
                $penyelesaianData[$peny][] = isset($penyelesaianByMonthly[$month][$peny]) ? $penyelesaianByMonthly[$month][$peny] : 0;
            }
        }
        return response()->json([
            'monthLabels' => $monthLabels,
            'totalTicketsMonthly' => array_values($totalTicketsMonthly),
            'allTechnicians' => $allTechnicians,
            'closedTicketsByTech' => $closedTicketsByTech,
            'avgResolutionTimeByTech' => $avgResolutionTimeByTech,
            'allJenisGangguan' => $allJenisGangguan,
            'gangguanByJenisData' => $gangguanByJenisData,
            'allPenyelesaian' => $allPenyelesaian,
            'penyelesaianData' => $penyelesaianData,
        ]);
    }

    public function getSession(Request $request)
    {
        $username = $request->query('username');
        $sessions = RadiusSession::where('shortname', multi_auth()->shortname)->where('username', $username)->with('ppp')->orderBy('id', 'desc')->first();
        return response()->json($sessions);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_gangguan' => 'required',
            'teknisi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $nomor_tiket = 'GGN-' . mt_rand(100000, 999999);
        $tiket = TiketGangguan::create([
            'shortname' => multi_auth()->shortname,
            'nomor_tiket' => $nomor_tiket,
            'pelanggan_id' => $request->pelanggan_id,
            'nama_pelanggan' => PppoeUser::where('id', $request->pelanggan_id)->first()->full_name,
            'jenis_gangguan' => $request->jenis_gangguan,
            'prioritas' => $request->prioritas,
            'note' => $request->note,
            'teknisi' => $request->teknisi,
            'created_by' => multi_auth()->username,
        ]);

        $pelanggan = PppoeUser::where('id', $request->pelanggan_id)->first();
        $teknisi = User::where('username', $request->teknisi)->first();
        $company = Company::where('shortname', multi_auth()->shortname)->first();
        $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
        $created_at = new \DateTime($tiket->created_at);
        $tanggal_laporan = $created_at->format('d/m/Y H:i');

        if ($pelanggan->wa !== null) {
            $shortcode = ['[tanggal_laporan]', '[nomor_tiket]', '[id_pelanggan]', '[nama_lengkap]', '[nomor_wa]', '[pop]', '[odp]', '[alamat]', '[jenis_gangguan]', '[prioritas]', '[note]', '[teknisi]', '[nomor_teknisi]', '[status_internet]', '[ip]'];
            $source = [$tanggal_laporan, $tiket->nomor_tiket, $pelanggan->id_pelanggan, $pelanggan->full_name, $pelanggan->wa, $pelanggan->kode_area, $pelanggan->kode_odp, $pelanggan->address, $request->jenis_gangguan, $tiket->prioritas, $tiket->note, $teknisi->name, $teknisi->whatsapp, $request->status_internet, $request->ip];
            $template = Watemplate::where('shortname', multi_auth()->shortname)->first()->tiket_open_pelanggan;
            $message = str_replace($shortcode, $source, $template);
            $message_format = str_replace('<br>', "\n", $message);

            try {
                $curl = curl_init();
                $data = [
                    'api_key' => $mpwa->api_key,
                    'sender' => $mpwa->sender,
                    'number' => $pelanggan->wa,
                    'message' => $message_format,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($curl);
                curl_close($curl);
                // $result = json_decode($response, true);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        if ($company->group_ggn !== null) {
            $shortcode = ['[tanggal_laporan]', '[nomor_tiket]', '[id_pelanggan]', '[nama_lengkap]', '[nomor_wa]', '[pop]', '[odp]', '[alamat]', '[jenis_gangguan]', '[prioritas]', '[note]', '[teknisi]', '[nomor_teknisi]', '[status_internet]', '[ip]'];
            $source = [$tanggal_laporan, $tiket->nomor_tiket, $pelanggan->id_pelanggan, $pelanggan->full_name, $pelanggan->wa, $pelanggan->kode_area, $pelanggan->kode_odp, $pelanggan->address, $request->jenis_gangguan, $tiket->prioritas, $tiket->note, $teknisi->name, $teknisi->whatsapp, $request->status_internet, $request->ip];
            $template = Watemplate::where('shortname', multi_auth()->shortname)->first()->tiket_open_teknisi;
            $message = str_replace($shortcode, $source, $template);
            $message_format = str_replace('<br>', "\n", $message);

            try {
                $curl = curl_init();
                $data = [
                    'api_key' => $mpwa->api_key,
                    'sender' => $mpwa->sender,
                    'number' => $company->group_ggn,
                    'message' => $message_format,
                ];
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($curl);
                curl_close($curl);
                // $result = json_decode($response, true);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket Berhasil Disubmit',
            'data' => $tiket,
        ]);
    }

    public function close(Request $request, $id)
    {
        $tiket_update = TiketGangguan::findOrFail($id);
        if ($request->status_internet === 'ONLINE') {
            $tiket_update->update([
                'status' => 'closed',
                'penyelesaian' => $request->penyelesaian,
                'closed_at' => Carbon::now(),
            ]);
            $tiket = TiketGangguan::where('id', $request->id)->first();
            $pelanggan = PppoeUser::where('id', $request->pelanggan_id)->first();
            $teknisi = User::where('username', $tiket->teknisi)->first();
            $company = Company::where('shortname', multi_auth()->shortname)->first();
            $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
            $updated_at = new \DateTime($tiket->updated_at);
            $tanggal_update = $updated_at->format('d/m/Y H:i');

            if ($pelanggan->wa !== null) {
                $shortcode = ['[tanggal_update]', '[nomor_tiket]', '[id_pelanggan]', '[nama_lengkap]', '[nomor_wa]', '[pop]', '[odp]', '[alamat]', '[jenis_gangguan]', '[prioritas]', '[note]', '[teknisi]', '[nomor_teknisi]', '[status_internet]', '[ip]', '[penyelesaian]'];
                $source = [$tanggal_update, $tiket->nomor_tiket, $pelanggan->id_pelanggan, $pelanggan->full_name, $pelanggan->wa, $pelanggan->kode_area, $pelanggan->kode_odp, $pelanggan->address, $tiket->jenis_gangguan, $tiket->prioritas, $tiket->note, $teknisi->name, $teknisi->whatsapp, $request->status_internet, $request->ip, $request->penyelesaian];
                $template = Watemplate::where('shortname', multi_auth()->shortname)->first()->tiket_close_pelanggan;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                try {
                    $curl = curl_init();
                    $data = [
                        'api_key' => $mpwa->api_key,
                        'sender' => $mpwa->sender,
                        'number' => $pelanggan->wa,
                        'message' => $message_format,
                    ];
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    // $result = json_decode($response, true);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }

            if ($company->group_ggn !== null) {
                $shortcode = ['[tanggal_update]', '[nomor_tiket]', '[id_pelanggan]', '[nama_lengkap]', '[nomor_wa]', '[pop]', '[odp]', '[alamat]', '[jenis_gangguan]', '[prioritas]', '[note]', '[teknisi]', '[nomor_teknisi]', '[status_internet]', '[ip]', '[penyelesaian]'];
                $source = [$tanggal_update, $tiket->nomor_tiket, $pelanggan->id_pelanggan, $pelanggan->full_name, $pelanggan->wa, $pelanggan->kode_area, $pelanggan->kode_odp, $pelanggan->address, $tiket->jenis_gangguan, $tiket->prioritas, $tiket->note, $teknisi->name, $teknisi->whatsapp, $request->status_internet, $request->ip, $request->penyelesaian];
                $template = Watemplate::where('shortname', multi_auth()->shortname)->first()->tiket_close_teknisi;
                $message = str_replace($shortcode, $source, $template);
                $message_format = str_replace('<br>', "\n", $message);

                try {
                    $curl = curl_init();
                    $data = [
                        'api_key' => $mpwa->api_key,
                        'sender' => $mpwa->sender,
                        'number' => $company->group_ggn,
                        'message' => $message_format,
                    ];
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    // $result = json_decode($response, true);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tiket Berhasil Diclose',
                'data' => $tiket_update,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tiket Gagal Diclose, Internet Pelanggan Masih Offline. Silakan Cek Kembali!',
            ]);
        }
    }

    public function destroy($id)
    {
        $tiket = TiketGangguan::findOrFail($id);
        $data = TiketGangguan::where('id', $id)->first();
        activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = multi_auth()->shortname;
            })
            ->event('Delete')
            ->log('Delete Tiket Gangguan: ' . $data->nomor_tiket . ' a.n ' . $data->nama_pelanggan);
        $tiket->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tiket Berhasil Dihapus',
            'data' => $tiket,
        ]);
    }

    public function export(Request $request)
    {
        $periode = Carbon::createFromFormat('F-Y', $request->periode);
        $periode = $periode->translatedFormat('F Y');
        $company = Company::where('shortname', multi_auth()->shortname)->first();
        if ($request->format === 'excel') {
            return Excel::download(new TransaksiExport($request), 'Laporan Keuangan ' . $company->name . ' - ' . $periode . '.xlsx');
        } elseif ($request->format === 'pdf') {
            $month = date('m', strtotime($request->periode));
            $year = date('Y', strtotime($request->periode));
            $transaksi = Transaksi::where('shortname', multi_auth()->shortname)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->get();
            $totalpemasukan = Transaksi::where('shortname', multi_auth()->shortname)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('tipe', 'Pemasukan')->sum('nominal');
            $totalpengeluaran = Transaksi::where('shortname', multi_auth()->shortname)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('tipe', 'Pengeluaran')->sum('nominal');
            $pdf = Pdf::loadView('backend.keuangan.transaksi.export.pdf', compact('transaksi', 'periode', 'totalpemasukan', 'totalpengeluaran', 'company'))->setPaper('a4', 'landscape'); // Paksa landscape
            return $pdf->download('Laporan Keuangan ' . $company->name . ' - ' . $periode . '.pdf');
        }
    }

    public function getGroup(Request $request)
    {
        $mpwa = Mpwa::where('shortname', multi_auth()->shortname)->first();
        try {
            $curl = curl_init();
            $data = [
                'api_key' => $mpwa->api_key,
                'device' => $mpwa->sender,
            ];
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/get-groups');
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'message' => 'Group Berhasil Ditampilkan',
            'data' => $result['data'],
        ]);
    }

    public function saveGroup(Request $request)
    {
        $company = Company::where('shortname', multi_auth()->shortname);
        $company->update([
            'group_ggn' => $request->group_id,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
        ]);
    }
}

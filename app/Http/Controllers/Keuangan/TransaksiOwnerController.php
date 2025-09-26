<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\KategoriKeuangan;
use Illuminate\Http\Request;
use App\Models\Keuangan\Transaksi;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Spatie\Activitylog\Contracts\Activity;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TransaksiExport;
use App\Models\Setting\Company;
use App\Models\Mikrotik\Nas;
use App\Models\Setting\Midtrans;
use App\Models\Setting\MidtransWithdraw;
use App\Models\User;
use App\Models\Whatsapp\Mpwa;

class TransaksiOwnerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $withdraw = MidtransWithdraw::query()->with('user');
            return DataTables::of($withdraw)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $wa_number = preg_replace('/[^0-9]/', '', $row->user->whatsapp); // hapus selain angka
                    if (substr($wa_number, 0, 1) === '0') {
                        $wa_number = '62' . substr($wa_number, 1); // ganti 0 di awal dengan 62
                    }
                    return '
                    <a href="javascript:void(0)" id="pay"
                        data-id="' . $row->id . '"
                        data-shortname="' . $row->shortname . '"
                        data-id_penarikan="' . $row->id_penarikan . '"
                        class="btn btn-primary text-white"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-currency-dollar me-1"></i> PAY
                    </a>
                
                    <a href="https://wa.me/' . $wa_number . '" target="_blank"
                        class="btn btn-success text-white"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-brand-whatsapp me-1"></i> WA
                    </a>
                
                    <a href="javascript:void(0)" id="delete"
                        data-id="' . $row->id . '"
                        class="btn btn-danger text-white"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-trash me-1"></i>
                    </a>';
                })
                ->toJson();
        }
        return view('backend.keuangan.withdraw.index_new');
    }

    public function pay(Request $request){
        $user = User::where('shortname',$request->shortname)->first();
        $wd = MidtransWithdraw::where('id',$request->id)->first();
        $midtrans_wd = MidtransWithdraw::where('id',$request->id);
        $midtrans_wd->update([
            'status' => 1,
        ]);
        $template = '👋 Hai, *' . $user->username . "*!<br><br>" .
    'Penarikan saldo midtrans berhasil diproses, berikut detailnya:<br><br>' .
    'ID Penarikan: `' . $wd->id_penarikan . "`<br>" .
    'Nominal: `Rp ' . number_format($wd->nominal, 0, ',', '.') . "`<br>" .
    'Rekening: `' . $wd->nomor_rekening . "`<br>" .
    'a.n: `' . $wd->atas_nama . "`<br><br>" .
    'Status: *SUKSES* ✅<br><br>' .
    'Terima kasih telah setia menggunakan billing Radiusqu. 🙏 ';
        $message_format = str_replace('<br>', "\n", $template);

        // ambil server pertama
        $wa_server = Mpwa::where('shortname', 'owner_radiusqu')->first();
        try {
            $curl = curl_init();
            $data = [
                'api_key' => $wa_server->api_key,
                'sender' => $wa_server->sender,
                'number' => $user->whatsapp,
                'message' => $message_format,
            ];
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, 'https://' . $wa_server->mpwa_server . '/send-message');
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($curl);
            curl_close($curl);
            // $result = json_decode($response, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return response()->json([
            'success' => true,
            'message' => 'Withdraw Berhasil Diproses',
            'data' => $midtrans_wd,
        ]);
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'id_data' => ['required', 'string', 'min:5', Rule::unique('keuangan_transaksi')->where('shortname', multi_auth()->shortname)],
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }

        $transaksi = Transaksi::create([
            'shortname' => multi_auth()->shortname,
            'id_data' => rand(00000, 99999),
            'tanggal' => $request->tanggal . ' ' . Carbon::now()->format('H:i:s'),
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'nominal' => str_replace('.', '', $request->nominal),
            'metode' => $request->metode,
            'created_by' => multi_auth()->username,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $transaksi,
        ]);
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $transaksi->update([
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'nominal' => str_replace('.', '', $request->nominal),
            'metode' => $request->metode,
        ]);
        activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = multi_auth()->shortname;
            })
            ->event('Update')
            ->log('Update Transaction: ' . $request->deskripsi . '');
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $transaksi,
        ]);
    }

    public function show(MidtransWithdraw $withdraw)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $withdraw,
        ]);
    }

    public function destroy($id)
    {
        $widthdraw = MidtransWithdraw::findOrFail($id);
        $widthdraw->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
            'data' => $widthdraw,
        ]);
    }

}

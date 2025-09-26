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

class TransaksiHotspotController extends Controller
{
    public function index()
    {
        $kategori_pemasukan = KategoriKeuangan::where('shortname', multi_auth()->shortname)->where('type', 'Pemasukan')->get();
        $kategori_pengeluaran = KategoriKeuangan::where('shortname', multi_auth()->shortname)->where('type', 'Pengeluaran')->get();

        $totalIncome = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('metode','Radius')->sum('nominal');
        $totalSaldo = $totalIncome;

        $incomeMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->where('metode','Radius')
            ->sum('nominal');

        $feeReseller = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('metode','Radius')
        ->whereYear('tanggal', Carbon::today()->year)
        ->whereMonth('tanggal', Carbon::today()->month)
        ->sum('fee_reseller');

        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $transaksi = Transaksi::query()
                    ->where('shortname', multi_auth()->shortname)
                    ->whereBetween('tanggal', [Carbon::parse($start_date)->format('Y-m-d 00:00:00'), Carbon::parse($end_date)->format('Y-m-d 23:59:59')]);
            } else {
                $transaksi = Transaksi::query()->where('shortname', multi_auth()->shortname)->where('metode','Radius');
            }
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->toJson();
        }
        return view('backend.keuangan.hotspot.index_new', compact('kategori_pemasukan', 'kategori_pengeluaran', 'incomeMonth', 'totalSaldo','feeReseller'));
    }


    public function show(Transaksi $transaksi)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $transaksi,
        ]);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $deskripsi = Transaksi::where('id', $id)->first();
        activity()
            ->tap(function (Activity $activity) {
                $activity->shortname = multi_auth()->shortname;
            })
            ->event('Delete')
            ->log('Delete Transaction: ' . $deskripsi->deskripsi . '');
        $transaksi->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
            'data' => $transaksi,
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

    public function pindahSaldo(Request $request)
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
            'tanggal' => Carbon::now(),
            'tipe' => 'Pemasukan',
            'kategori' => 'Midtrans',
            'deskripsi' => 'Pemindahan Saldo Midtrans',
            'nominal' => str_replace('.', '', $request->nominal),
            'metode' => 'Transfer',
            'created_by' => 'frradius',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Saldo Berhasil Dipindahkan',
            'data' => $transaksi,
        ]);
    }
}

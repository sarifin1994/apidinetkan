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
use App\Models\Setting\Mduitku;

class TransaksiDuitkuController extends Controller
{
    public function index()
    {
        $duitku = Mduitku::where('shortname', multi_auth()->shortname)->first();
        $incomeMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->where('created_by','duitku')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->sum('nominal');
        $incomeLastMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->where('created_by','duitku')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->subMonth()->month)
            ->sum('nominal');
        $incomeYear = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->where('created_by','duitku')
            ->whereYear('tanggal', Carbon::today()->year)
            ->sum('nominal');

            $totalIncome = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->where('created_by','duitku')
            ->sum('nominal');
            $totalExpense = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->where('created_by','frradius')
            ->sum('nominal');
            $totalSaldo = $totalIncome - $totalExpense;
        // $expenseMonth = Transaksi::where('shortname', multi_auth()->shortname)
        //     ->where('tipe', 'Pengeluaran')
        //     ->whereYear('tanggal', Carbon::today()->year)
        //     ->whereMonth('tanggal', Carbon::today()->month)
        //     ->sum('nominal');

        // $incomeDay = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', Carbon::today())->sum('nominal');
        // $expenseDay = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->whereDate('tanggal', Carbon::today())->sum('nominal');

        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $transaksi = Transaksi::query()
                    ->where('shortname', multi_auth()->shortname)
                    ->where('created_by','duitku')
                    ->whereBetween('tanggal', [Carbon::parse($start_date)->format('Y-m-d 00:00:00'), Carbon::parse($end_date)->format('Y-m-d 23:59:59')]);
            } else {
                $transaksi = Transaksi::query()->where('shortname', multi_auth()->shortname)->where('created_by','duitku')
                ;
            }
            return DataTables::of($transaksi)
                ->addIndexColumn()
               ->addColumn('action', function ($row) {
                    if ($row->created_by === 'duitku') {
                        return '
                            <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '" 
                            class="btn btn-danger text-white d-inline-flex align-items-center justify-content-center"
                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-trash"></i>
                            </a>';
                    }
                })
                ->toJson();
        }
        return view('backend.keuangan.duitku.index_new', compact('incomeMonth','incomeLastMonth','incomeYear','totalSaldo','duitku'));
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
            'kategori' => 'Duitku',
            'deskripsi' => 'Pemindahan Saldo Duitku',
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

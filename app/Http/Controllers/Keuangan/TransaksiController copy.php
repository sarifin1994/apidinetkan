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

class TransaksiController extends Controller
{
    public function index()
    {
        $kategori_pemasukan = KategoriKeuangan::where('shortname', multi_auth()->shortname)->where('type', 'Pemasukan')->get();
        $kategori_pengeluaran = KategoriKeuangan::where('shortname', multi_auth()->shortname)->where('type', 'Pengeluaran')->get();

        $totalIncome = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereNot('created_by','midtrans')->whereNot('created_by','duitku')->sum('nominal');
        $totalExpense = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->whereNot('created_by','midtrans')->whereNot('created_by','duitku')->sum('nominal');
        $totalBalance = $totalIncome - $totalExpense;

        $totalIncomeCash = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('metode', 'Cash')->sum('nominal');
        $totalExpenseCash = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->where('metode', 'Cash')->sum('nominal');
        $totalCash = $totalIncomeCash - $totalExpenseCash;

        $totalIncomeTransfer = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('metode', 'Transfer')->sum('nominal');
        $totalExpenseTransfer = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->where('metode', 'Transfer')->sum('nominal');
        $totalTransfer = $totalIncomeTransfer - $totalExpenseTransfer;

        $incomeMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->whereNot('created_by','midtrans')
            ->whereNot('created_by','duitku')
            ->sum('nominal');
        $expenseMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pengeluaran')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->whereNot('created_by','midtrans')
            ->whereNot('created_by','duitku')
            ->sum('nominal');

        $incomeDay = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereNot('created_by','midtrans')->whereNot('created_by','duitku')
        ->whereDate('tanggal', Carbon::today())->sum('nominal');
        $expenseDay = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->whereNot('created_by','midtrans')->whereNot('created_by','duitku')->whereDate('tanggal', Carbon::today())->sum('nominal');

        $dataBulan = [];
        $dataNas = [];
        $bulanSekarang = date('m');
        $tahun = date('Y');

        // Buat array nama bulan dari awal tahun sampai bulan berjalan
        for ($i = 1; $i <= $bulanSekarang; $i++) {
            $dataBulan[] = Carbon::create()->month($i)->format('F');
        }

        // Ambil data nas sesuai shortname user
        $nasItems = Nas::where('shortname', multi_auth()->shortname)->get();

        // Untuk tiap nas, hitung total pemasukan (income) per bulan
        foreach ($nasItems as $item) {
            $incomePerMonth = [];
            for ($i = 1; $i <= $bulanSekarang; $i++) {
                $income = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('kategori', 'Hotspot')->where('nas', $item->name)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');

                $incomePerMonth[] = $income;
            }
            // Simpan data income berdasarkan nas name
            $dataNas[$item->name] = $incomePerMonth;
        }

        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $transaksi = Transaksi::query()
                    ->where('shortname', multi_auth()->shortname)
                    ->whereBetween('tanggal', [Carbon::parse($start_date)->format('Y-m-d 00:00:00'), Carbon::parse($end_date)->format('Y-m-d 23:59:59')]);
            } else {
                $transaksi = Transaksi::query()->where('shortname', multi_auth()->shortname)->whereNot('created_by','midtrans')->whereNot('created_by','duitku')->whereNot('metode','Radius');
            }
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->created_by === 'frradiussss') {
                    } else {
                        return '
                    <a href="javascript:void(0)" id="edit"
                    data-id="' .
                            $row->id .
                            '" class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <span class="material-symbols-outlined">edit</span>
                </a>
                <a href="javascript:void(0)" id="delete" data-id="' .
                            $row->id .
                            '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <span class="material-symbols-outlined">delete</span>
                </a>';
                    }
                })
                ->toJson();
        }
        return view('backend.keuangan.transaksi.index', compact('kategori_pemasukan', 'kategori_pengeluaran', 'incomeMonth', 'expenseMonth', 'totalBalance', 'totalCash', 'totalTransfer', 'incomeDay', 'expenseDay', 'dataBulan', 'dataNas'));
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

        $totalIncome = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->sum('nominal');
        $totalExpense = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->sum('nominal');
        $totalBalance = $totalIncome - $totalExpense;

        $totalIncomeCash = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('metode', 'Cash')->sum('nominal');
        $totalExpenseCash = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->where('metode', 'Cash')->sum('nominal');
        $totalCash = $totalIncomeCash - $totalExpenseCash;

        $totalIncomeTransfer = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->where('metode', 'Transfer')->sum('nominal');
        $totalExpenseTransfer = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->where('metode', 'Transfer')->sum('nominal');
        $totalTransfer = $totalIncomeTransfer - $totalExpenseTransfer;

        $incomeMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pemasukan')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->sum('nominal');
        $expenseMonth = Transaksi::where('shortname', multi_auth()->shortname)
            ->where('tipe', 'Pengeluaran')
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->sum('nominal');

        $incomeDay = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', Carbon::today())->sum('nominal');
        $expenseDay = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->whereDate('tanggal', Carbon::today())->sum('nominal');

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
            $transaksi = Transaksi::where('shortname', multi_auth()->shortname)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereNot('created_by','frradius')->get();
            $totalpemasukan = Transaksi::where('shortname', multi_auth()->shortname)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('tipe', 'Pemasukan')->whereNot('created_by','frradius')->sum('nominal');
            $totalpengeluaran = Transaksi::where('shortname', multi_auth()->shortname)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('tipe', 'Pengeluaran')->whereNot('created_by','frradius')->sum('nominal');
            $pdf = Pdf::loadView('backend.keuangan.transaksi.export.pdf', compact('transaksi', 'periode', 'totalpemasukan', 'totalpengeluaran', 'company'))->setPaper('a4', 'landscape'); // Paksa landscape
            return $pdf->download('Laporan Keuangan ' . $company->name . ' - ' . $periode . '.pdf');
        }
    }
}

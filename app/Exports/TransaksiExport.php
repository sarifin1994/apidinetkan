<?php

namespace App\Exports;

use App\Models\Keuangan\Transaksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings, WithStyles,ShouldAutoSize, WithMapping, WithColumnFormatting, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $month = date('m', strtotime($this->request->periode));
        $year = date('Y', strtotime($this->request->periode)); 
        $query = Transaksi::query()->where('shortname',multi_auth()->shortname);

        if ($this->request->has('periode')) {
            $query->whereMonth('tanggal',$month)->whereYear('tanggal',$year)->whereNot('created_by','frradius');
        }

        return $query->get(['tanggal', 'tipe', 'kategori', 'deskripsi','nominal','fee_reseller','metode','reseller','created_by']);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi','Nominal','Fee Reseller','Metode','Reseller','Created By'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Bold untuk header
        ];
    }

    public function map($row): array
    {
        // Ubah field tanggal menjadi format d-m-Y tanpa waktu
        $tanggal = Carbon::parse($row->tanggal)->format('d-m-Y');

        return [
            $tanggal,
            $row->tipe,
            $row->kategori,
            $row->deskripsi,
            $row->nominal,
            $row->fee_reseller,
            $row->metode,
            $row->reseller,
            $row->created_by,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => '#,##0_-', // Format Rupiah tanpa koma desimal
            'F' => '#,##0_-', // Format Rupiah tanpa koma desimal
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Mengatur header jadi bold dan rata tengah
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                
                // Mengatur lebar kolom otomatis
                foreach (range('A', 'G') as $col) {
                    $event->sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

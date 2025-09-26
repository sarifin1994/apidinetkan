<?php

namespace App\Exports;
use App\Models\Invoice\Invoice;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoiceExport implements FromCollection,WithMapping,WithHeadings
{
    protected $id;

    function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Invoice::whereIn('id',$this->id)->with('rpppoe')->get();
    }

    public function map($invoice) : array {
        static $no = 1; 
        $harga = $invoice->price;        // misal 10000
        $diskon = $invoice->discount;      // misal 10 (10%)
        $ppn = $invoice->ppn;              // misal 11 (11%)
        
        // Menghitung harga setelah diskon
        $hargaSetelahDiskon = $harga - ($harga * $diskon / 100);
        
        // Menghitung nilai PPN berdasarkan harga setelah diskon
        $nilaiPPN = $hargaSetelahDiskon * $ppn / 100;
        
        // Total akhir adalah harga setelah diskon ditambah nilai PPN
        $total = $hargaSetelahDiskon + $nilaiPPN;
        
        // Rumus singkat: total = price * (1 - discount/100) * (1 + ppn/100)
        return [
            $no++,
            $invoice->rpppoe->full_name,
            $invoice->rpppoe->kode_area,
            $invoice->rpppoe->alamat,
            $invoice->rpppoe->wa,
            $invoice->no_invoice,
            date('d/m/Y', strtotime($invoice->due_date)),
            $invoice->subscribe,
            $total,
            $invoice->status,
        ] ;
 
 
    }
 
    public function headings() : array {
        return [
           'No',
           'Nama Lengkap',
           'Kode Area',
           'Alamat',
           'Whatsapp',
           'No Invoice',
           'Jatuh Tempo',
           'Periode Langganan',
           'Total',
           'Satus',
        ] ;
    }
}

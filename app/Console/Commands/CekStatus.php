<?php

namespace App\Console\Commands;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Models\AdminInvoice;
use App\Models\GrafikMikrotik;
use App\Models\MasterMikrotik;
use App\Models\UserDinetkan;
use App\Models\WatemplateDinetkan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RouterOS\Client;
use RouterOS\Query;

class CekStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cekstatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cek status cron';


    /**
     * Execute the console command.
     */
    public function handle()
    {
//        Log::info("get grafik mikrotik success ". \Illuminate\Support\Carbon::now()->format('Y-m-d H:i'));
//        $dinetkan = UserDinetkan::where('username','dinetkan')->get();
//        foreach ($dinetkan as $row){
//            $tenplate = WatemplateDinetkan::where('dinetkan_user_id', $row->dinetkan_user_id)->first();
//            if(!$tenplate){
//                WatemplateDinetkan::create([
//                    'dinetkan_user_id' => $row->dinetkan_user_id,
//                    'shortname' => $row->shortname,
//                    'invoice_terbit' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Invoice anda sudah terbit. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
//                    'invoice_reminder' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Notifikasi ini hanya reminder untuk internet anda yang belum dibayar. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
//                    'invoice_overdue' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, Internet anda saat ini diisolir oleh *Bililng System* kami dikarenakan keterlambatan pembayaran.<br><br>Untuk dapat terus menikmati layanan internet kami, segera lakukan pembayaran senilai Rp. [total]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
//                    'payment_paid' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Terimakasih, pembayaran internet anda telah kami terima. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Status: *LUNAS*<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
//                    'payment_cancel' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, pembayaran Internet Anda senilai *Rp. [total]* untuk periode *[periode]* telah kami batalkan, silakan hubungi admin untuk informasi lebih lanjut.<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
//                    'account_active' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Selamat internet anda sudah aktif! Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>Paket Internet : [paket_internet]<br>Harga : Rp. [harga]<br>Tipe Pembayaran : [tipe_pembayaran]<br>Jatuh Tempo : [jth_tempo]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
//                    'account_suspend' => 'Account Suspend',
//                    'tiket_open_pelanggan' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Tiket gangguan berhasil dibuat. Berikut rinciannya<br><br>Tanggal Laporan : [tanggal_laporan]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br><br>Teknisi yang Ditugaskan : [teknisi]<br>Nomor Teknisi : [nomor_teknisi]<br><br>Mohon maaf atas ketidaknyamanannya,<br>Teknisi kami akan segera menghubungi Anda untuk konfirmasi perbaikan',
//                    'tiket_open_teknisi' => '*Tiket Gangguan Baru* Mohon Segera Dikerjakan!<br><br>*Data Gangguan*<br>Tanggal Laporan : [tanggal_laporan]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Prioritas : [prioritas]<br>Keterangan : [note]<br>Status Internet : [status_internet]<br>IP Address : [ip]<br><br>*Data Pelanggan*<br>Nama Pelanggan : [nama_lengkap]<br>Nomor Whatsapp : [nomor_wa]<br>POP / ODP : [pop] / [odp]<br>Alamat : [alamat]<br><br>Teknisi yang Ditugaskan : *[teknisi]*<br><br>Harap close tiket di dashboard dev.Dinetkan.com jika perbaikan gangguan sudah selesai.<br>Terimakasih',
//                    'tiket_close_pelanggan' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Tiket gangguan berhasil diperbaiki dan internet Anda sudah online kembali. Berikut rinciannya<br><br>Tanggal Update : [tanggal_update]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br><br>Teknisi : [teknisi]<br><br>Mohon konfirmasinya apabila internet Anda masih belum bisa digunakan<br>Terimakasih',
//                    'tiket_close_teknisi' => '*Tiket Gangguan* Berhasil Diperbaiki!<br><br>Nama Pelanggan : [nama_lengkap] <br>Tanggal Update : [tanggal_update]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br>Status Tiket : CLOSED<br>Teknisi : [teknisi]<br><br>Terimakasih atas kinerja Anda, semoga sehat selalu',
//                ]);
//            }
//        }
    }
}

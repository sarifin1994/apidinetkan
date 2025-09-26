<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WablasTemplate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\WablasTemplate::create([
            'group_id' => 0,
            'invoice_terbit' => 'Kepada Pelanggan Yth<br>Bpk/Ibu *[nama_lengkap]*<br><br>Kami informasikan tagihan internet Anda untuk bulan ini sudah terbit.<br>Berikut detailnya<br><br>ID Pelanggan: [id_pelanggan]<br>Paket Internet: [paket_internet]<br>Periode: [periode]<br>Total Tagihan: Rp. [total]<br>Jatuh Tempo: [jth_tempo]<br><br>Pembayaran bisa dilakukan secara cash atau transfer ke rekening berikut<br>Mandiri 1320022965751 a.n *PT. Putra Garsel Interkoneksi*<br><br>_Abaikan pesan ini jika sudah membayar_<br>_Terima kasih, semoga senantiasa sehat dan selalu dilimpahkan rezekinya_',
            'invoice_reminder' => 'Kepada Pelanggan Yth<br>Bpk/Ibu *[nama_lengkap]*<br><br>Kami informasikan tagihan internet Anda senilai *Rp. [total]* akan segera jatuh tempo pada tanggal [jth_tempo]<br><br>Pembayaran bisa dilakukan secara cash atau transfer ke rekening berikut<br>Mandiri 1320022965751 a.n *PT. Putra Garsel Interkoneksi*<br><br>_Abaikan pesan ini jika sudah membayar_<br>_Terima kasih, semoga senantiasa sehat dan selalu dilimpahkan rezekinya_',
            'invoice_overdue' => 'Kepada Pelanggan Yth<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, Internet Anda saat ini telah ditangguhkan (Isolir) oleh *System Billing* kami, dikarenakan keterlambatan dalam pembayaran.<br><br>Saat ini Anda tidak dapat menggunakan internet, sampai anda menyelesaikan pembayaran senilai Rp. [total]<br><br>Pembayaran bisa dilakukan secara cash atau transfer ke rekening berikut<br>Mandiri 1320022965751 a.n *PT. Putra Garsel Interkoneksi*<br><br>_Terima kasih, semoga senantiasa sehat dan selalu dilimpahkan rezekinya_',
            'payment_paid' => 'Kepada Pelanggan Yth<br>Bpk/Ibu *[nama_lengkap]*<br><br>Pembayaran Internet Anda telah kami terima, berikut rinciannya<br><br>ID Pelanggan: [id_pelanggan]<br>Paket Internet: [paket_internet]<br>Periode: [periode]<br>Total: Rp. [total]<br><br>Status: *LUNAS*<br>Metode Pembayaran: [paid_method]<br><br>_Terima kasih atas pembayarannya, semoga senantiasa sehat dan selalu dilimpahkan rezekinya_',
            'payment_cancel' => 'Kepada Pelanggan Yth<br>Bpk/Ibu *[nama_lengkap]*<br><br>Status pembayaran Internet Anda untuk periode [periode] telah kami batalkan, silakan hubungi admin untuk informasi lebih lanjut.<br><br>_Terima kasih, semoga senantiasa sehat dan selalu dilimpahkan rezekinya_',
            'account_active' => 'Congratulations! Your Account Has Been Actived',
            'account_suspend' => 'Sorry! Your Account Has Been Suspended',
        ]);
    }
}

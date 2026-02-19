<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CountNumbering;
use App\Models\LicenseDinetkan;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Models\WatemplateDinetkan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrasiDinetkan extends Controller
{
    public function store(Request $request){
        $users = User::query()->get();
        foreach ($users as $user){
            if (gantiformat_hp($user->whatsapp) == gantiformat_hp(trim($request->whatsapp))) {
                return response()->json(['message' => 'Data Whatsapp sudah ada'], 500);
            }
            if (strtolower($user->email) == strtolower(trim($request->email))) {
                return response()->json(['message' => 'Data Email sudah ada'], 500);
            }
            if (strtolower($user->username) == strtolower(trim($request->email))) {
                return response()->json(['message' => 'Data Email sudah ada'], 500);
            }
        }
        DB::beginTransaction();
        try{
            $user = UserDinetkan::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'shortname' => Str::trim($request->first_name).Str::trim($request->last_name),
                'name' => $request->first_name." ".$request->last_name,
                'role' => 'Admin',
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'username' => $request->email,
                'id_card' => $request->id_card,
                'npwp' => $request->npwp,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'is_reguler' => 0,
                'address' => $request->address,

                //    case DISABLED = 0;
                //    case ACTIVE = 1;
                //    case SUSPEND = 2;
                //    case EXPIRED = 3;
                //    case NEW = 4;
                //    case ACCEPT = 4;

                'status' => 4,
                'license_id' => 3,
                'next_due' => '9999-12-31', //null,
                'dinetkan_user_id' => $this->generateMemberId(),
                'is_dinetkan' => 1,
                'request_license_id' => $request->request_license_id
            ]);
            WatemplateDinetkan::create([
                'dinetkan_user_id' => $user->dinetkan_user_id,
                'shortname' => $user->shortname,
                'invoice_terbit' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Invoice anda sudah terbit. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
                'invoice_reminder' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Notifikasi ini hanya reminder untuk internet anda yang belum dibayar. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
                'invoice_overdue' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, Internet anda saat ini diisolir oleh *Bililng System* kami dikarenakan keterlambatan pembayaran.<br><br>Untuk dapat terus menikmati layanan internet kami, segera lakukan pembayaran senilai Rp. [total]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
                'payment_paid' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Terimakasih, pembayaran internet anda telah kami terima. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Status: *LUNAS*<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
                'payment_cancel' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, pembayaran Internet Anda senilai *Rp. [total]* untuk periode *[periode]* telah kami batalkan, silakan hubungi admin untuk informasi lebih lanjut.<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
                'account_active' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Selamat internet anda sudah aktif! Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>Paket Internet : [paket_internet]<br>Harga : Rp. [harga]<br>Tipe Pembayaran : [tipe_pembayaran]<br>Jatuh Tempo : [jth_tempo]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Dinetkan',
                'account_suspend' => 'Account Suspend',
                'tiket_open_pelanggan' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Tiket gangguan berhasil dibuat. Berikut rinciannya<br><br>Tanggal Laporan : [tanggal_laporan]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br><br>Teknisi yang Ditugaskan : [teknisi]<br>Nomor Teknisi : [nomor_teknisi]<br><br>Mohon maaf atas ketidaknyamanannya,<br>Teknisi kami akan segera menghubungi Anda untuk konfirmasi perbaikan',
                'tiket_open_teknisi' => '*Tiket Gangguan Baru* Mohon Segera Dikerjakan!<br><br>*Data Gangguan*<br>Tanggal Laporan : [tanggal_laporan]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Prioritas : [prioritas]<br>Keterangan : [note]<br>Status Internet : [status_internet]<br>IP Address : [ip]<br><br>*Data Pelanggan*<br>Nama Pelanggan : [nama_lengkap]<br>Nomor Whatsapp : [nomor_wa]<br>POP / ODP : [pop] / [odp]<br>Alamat : [alamat]<br><br>Teknisi yang Ditugaskan : *[teknisi]*<br><br>Harap close tiket di dashboard dev.Dinetkan.com jika perbaikan gangguan sudah selesai.<br>Terimakasih',
                'tiket_close_pelanggan' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Tiket gangguan berhasil diperbaiki dan internet Anda sudah online kembali. Berikut rinciannya<br><br>Tanggal Update : [tanggal_update]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br><br>Teknisi : [teknisi]<br><br>Mohon konfirmasinya apabila internet Anda masih belum bisa digunakan<br>Terimakasih',
                'tiket_close_teknisi' => '*Tiket Gangguan* Berhasil Diperbaiki!<br><br>Nama Pelanggan : [nama_lengkap] <br>Tanggal Update : [tanggal_update]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br>Status Tiket : CLOSED<br>Teknisi : [teknisi]<br><br>Terimakasih atas kinerja Anda, semoga sehat selalu',
            ]);


            Company::create([
                'group_id' => $user->id,
                'name' => $request->company_name,
                'nickname' => 'Dinetkan',
                'email' => $request->email,
                'wa' => $request->whatsapp,
                'address' => 'Bandung, Jawa Barat, Indonesia',
                'logo' => 'favicon3.png',
            ]);
            DB::commit();
            return response()->json([
                'data' => $user,
                'message' => 'Data berhasil dibuat'
            ], 200);
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json([
                'data' => "",
                'message' => 'Data gagal dibuat '.$ex->getMessage()
            ], 500);
        }

    }



    public function generateMemberId(): string
    {
        // Get the latest member ID or start from 0
        $prefix = Carbon::now()->format('Ym');
        $lastMember = CountNumbering::where('tipe', 'user_dinetkan')
            ->first();
        $lastNumber = $lastMember ? (int)$lastMember->count : 0;
        $nextNumber = $lastNumber + 1;

        // Format to 8 digits with leading zeros

        $lastMember->count = $nextNumber;
        $lastMember->save();
        $userid = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $userid;
    }

    public function list_licensi(Request $request){
        $license = LicenseDinetkan::query()->get();
        $license = $license->map(function($e){
            return [
                'id' => $e->id,
                'name' => $e->name
            ];
        });
        return response()->json($license);
    }
}

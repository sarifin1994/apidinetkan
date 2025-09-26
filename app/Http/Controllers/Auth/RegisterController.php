<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SettingTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use App\Models\User;
use Carbon\Carbon;
use Session;
use App\Models\Whatsapp\Mpwa;

class RegisterController extends Controller
{
    public function index()
    {
        $setting = SettingTable::query()->where('group', 'site')->where('name', 'allow_register')->first();
        if($setting->payload == 0){
            return view('backend.auth.not_allow');
        }
        return view('backend.auth.register_new');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'regex:/^[a-z0-9]+$/', 'min:5', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'whatsapp' => ['required', 'string', 'min:10', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        $user = User::create([
            'shortname' => $request->username,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),
            'role' => 'Admin',
            // 1 active 0 disable //2 pending 3 expired
            'status' => 1, // pending
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
            'next_due' => Carbon::now()->addWeeks(1)->format('Y-m-d')
        ]);

        \App\Models\Setting\BillingSetting::create([
            'shortname' => $user->shortname,
            'due_bc' => 20,
            'inv_fd' => 1,
            'suspend_date' => 1,
            'suspend_time' => '00:00:00',
            'notif_ir' => 0,
            'notif_it' => 0,
            'notif_ps' => 0,
            'notif_sm' => 0,
        ]);
        \App\Models\Setting\Company::create([
            'shortname' => $user->shortname,
            'name' => 'Radiusqu Network',
        ]);
        \App\Models\Setting\Isolir::create([
            'shortname' => $user->shortname,
            'isolir' => 0,
            'type' => 'pppoe',
        ]);
        \App\Models\Whatsapp\Watemplate::create([
            'shortname' => $user->shortname,
            'invoice_terbit' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Invoice anda sudah terbit. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Keterangan Adons : [description_adon]<br>Total Adons : Rp. [total_adons]<br>total Invoice : Rp. [total_invoice]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Radiusqu',
            'invoice_reminder' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Notifikasi ini hanya reminder untuk internet anda yang belum dibayar. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Keterangan Adons : [description_adon]<br>Total Adons : Rp. [total_adons]<br>total Invoice : Rp. [total_invoice]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Radiusqu',
            'invoice_overdue' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, Internet anda saat ini diisolir oleh *Bililng System* kami dikarenakan keterlambatan pembayaran.<br>Keterangan Adons : [description_adon]<br>Total Adons : Rp. [total_adons]<br>total Invoice : Rp. [total_invoice]<br><br>Untuk dapat terus menikmati layanan internet kami, segera lakukan pembayaran senilai Rp. [total]<br><br>Pembayaran bisa dilakukan secara tunai atau transfer melalui link berikut [link_pembayaran] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Radiusqu',
            'payment_paid' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Terimakasih, pembayaran internet anda telah kami terima. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Keterangan Adons : [description_adon]<br>Total Adons : Rp. [total_adons]<br>total Invoice : Rp. [total_invoice]<br>Total : Rp. [total]<br>Status: *LUNAS*<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Radiusqu',
            'payment_cancel' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, pembayaran Internet Anda senilai *Rp. [total]* untuk periode *[periode]* telah kami batalkan, silakan hubungi admin untuk informasi lebih lanjut.<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Radiusqu',
            'account_active' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Selamat internet anda sudah aktif! Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>Paket Internet : [paket_internet]<br>Harga : Rp. [harga]<br>Tipe Pembayaran : [tipe_pembayaran]<br>Jatuh Tempo : [jth_tempo]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>Radiusqu',
            'account_suspend' => 'Account Suspend',
            'tiket_open_pelanggan' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Tiket gangguan berhasil dibuat. Berikut rinciannya<br><br>Tanggal Laporan : [tanggal_laporan]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br><br>Teknisi yang Ditugaskan : [teknisi]<br>Nomor Teknisi : [nomor_teknisi]<br><br>Mohon maaf atas ketidaknyamanannya,<br>Teknisi kami akan segera menghubungi Anda untuk konfirmasi perbaikan',
            'tiket_open_teknisi' => '*Tiket Gangguan Baru* Mohon Segera Dikerjakan!<br><br>*Data Gangguan*<br>Tanggal Laporan : [tanggal_laporan]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Prioritas : [prioritas]<br>Keterangan : [note]<br>Status Internet : [status_internet]<br>IP Address : [ip]<br><br>*Data Pelanggan*<br>Nama Pelanggan : [nama_lengkap]<br>Nomor Whatsapp : [nomor_wa]<br>POP / ODP : [pop] / [odp]<br>Alamat : [alamat]<br><br>Teknisi yang Ditugaskan : *[teknisi]*<br><br>Harap close tiket di dashboard '.env('APP_URL').' jika perbaikan gangguan sudah selesai.<br>Terimakasih',
            'tiket_close_pelanggan' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Tiket gangguan berhasil diperbaiki dan internet Anda sudah online kembali. Berikut rinciannya<br><br>Tanggal Update : [tanggal_update]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br><br>Teknisi : [teknisi]<br><br>Mohon konfirmasinya apabila internet Anda masih belum bisa digunakan<br>Terimakasih',
            'tiket_close_teknisi' => '*Tiket Gangguan* Berhasil Diperbaiki!<br><br>Nama Pelanggan : [nama_lengkap] <br>Tanggal Update : [tanggal_update]<br>Nomor Tiket : [nomor_tiket]<br>Jenis Gangguan : [jenis_gangguan]<br>Status Internet : [status_internet]<br>Status Tiket : CLOSED<br>Teknisi : [teknisi]<br><br>Terimakasih atas kinerja Anda, semoga sehat selalu',
        ]);
        \App\Models\Setting\Midtrans::create([
            'shortname' => $user->shortname,
            'client_key' => "", // env('CLIENT_MIDTRANS'),
            'server_key' => "", // env('SERVER_MIDTRANS'),
            'id_merchant' => "", // env('MERCHANT_MIDTRANS'),
            'status' => 1,
        ]);
        \App\Models\Keuangan\KategoriKeuangan::create([
            'shortname' => $user->shortname,
            'category' => 'Invoice',
            'type' => 'Pemasukan',
            'status' => '0',
        ]);
        \App\Models\Keuangan\KategoriKeuangan::create([
            'shortname' => $user->shortname,
            'category' => 'Hotspot',
            'type' => 'Pemasukan',
            'status' => '0',
        ]);
        $username = $user->username;

        $this->sendOtpWhatsApp($user->whatsapp, $otp, $username);
        $this->sendOtpEmail($user->email, $otp, $username);

        return redirect('/')->with('whatsapp', $user->whatsapp);
    }

    public function resendOtp(Request $request)
    {
        $user = User::where('whatsapp', $request->whatsapp);
        $username = User::where('whatsapp', $request->whatsapp)->first()->username;
        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);
        $this->sendOtpWhatsapp($request->whatsapp, $otp, $username);
        $this->sendOtpEmail($user->email, $otp, $username);

        return response()->json(['message' => 'OTP berhasil dikirim ulang']);
    }

    private function sendOtpWhatsapp($whatsapp, $otp, $username)
    {
        $message = <<<MSG
        👋 Hai, *{$username}*

        Kode OTP untuk pendaftaran akun Radiusqu Anda adalah *{$otp}*. `Hanya berlaku 5 menit`
        MSG;
        // ambil server pertama
        $wa_server = Mpwa::where('shortname','owner_radiusqu')->first();
        try {
            $curl = curl_init();
            $data = [
                'api_key' => $wa_server->api_key,
                'sender' => $wa_server->sender,
                'number' => $whatsapp,
                'message' => $message,
            ];
            $urlapi = 'https://' . $wa_server->mpwa_server . '/send-message';
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, $urlapi);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($curl);
            Log::info($message);
            curl_close($curl);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }



    private function sendOtpEmail($email, $otp, $username)
    {
        $message = "Hai, {$username} Kode OTP untuk pendaftaran akun Radiusqu Anda adalah *{$otp}*. `Hanya berlaku 5 menit";

        try {
            Mail::raw($message, function ($message) use ($email) {
                $message->to($email)
                    ->subject('OTP LOGIN');
            });

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function gantiNomorKeInternasional($nomor) {
        // Jika nomor dimulai dengan 08, ganti dengan 62
        if (substr($nomor, 0, 2) === '08') {
            return '628' . substr($nomor, 2);
        }
        return $nomor; // Kembalikan apa adanya jika tidak dimulai dengan 0
    }

    public function verify()
    {
        return view('backend.auth.verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required',
            'otp' => 'required|digits:6',
        ]);
        session(['whatsapp' => $request->whatsapp]);
        $user = User::where('whatsapp', $request->whatsapp)->where('otp', $request->otp)->first();

        if (!$user || $user->otp_expires_at < Carbon::now()) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.']);
        }

        // Hapus OTP setelah verifikasi berhasil
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'next_due' => \Carbon\Carbon::today()->addDays(3)->format('Y-m-d'),
            'status' => 1,
        ]);

        session()->forget('phone');
        if($user->is_dinetkan == 1){
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'next_due' => \Carbon\Carbon::today()->addDays(3)->format('Y-m-d'),
                'status' => 1,
            ]);
//            return redirect()->to('/admin/account/get_info_dinetkan')->with('success', 'Registrasi berhasil, silakan login..');
        }
        return redirect()->to('auth')->with('success', 'Registrasi berhasil, silakan login..');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Owner\UserDinetkanRequest;
use App\Models\CountNumbering;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Models\Wablas;
use App\Models\Company;
use App\Models\Midtrans;
use App\Models\TelegramBot;
use App\Models\PppoeSetting;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use App\Models\Whatsapp\WatemplateDinetkan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Tripay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterDinetkanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = '/after_regis/get_info_dinetkan';
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
//            'username' => ['required', 'string', 'lowercase', 'regex:/^[a-z0-9]+$/', 'min:5', 'max:255', 'unique:users'],
            'company_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'whatsapp' => ['required', 'string', 'min:10', 'max:15'],
            'password' => ['required', 'string', 'min:6', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);
    }

    public function generateMemberId(): string
    {
        // Get the latest member ID or start from 0
        $lastMember = CountNumbering::where('tipe', 'user_dinetkan')
            ->first();
        $lastNumber = $lastMember ? (int)$lastMember->count : 0;
        $nextNumber = $lastNumber + 1;

        // Format to 8 digits with leading zeros

        $lastMember->count = $nextNumber;
        $lastMember->save();
        $userid = Carbon::now()->format('Ym').str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $userid;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $otp = rand(100000, 999999);
        $user = User::create([
//            'id_group' => User::max('id_group') + 1,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'shortname' => Str::trim($data['first_name']).Str::trim($data['last_name']),
            'name' => $data['first_name']." ".$data['last_name'],
            'role' => 'Admin',
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'],
            'username' => $data['email'],
            'password' => Hash::make($data['password']),

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
            'otp' => $otp,
            'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(5),
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
            'name' => $data['company_name'],
            'nickname' => 'Dinetkan',
            'email' => $data['email'],
            'wa' => $data['whatsapp'],
            'address' => 'Bandung, Jawa Barat, Indonesia',
            'logo' => 'favicon3.png',
        ]);

        event(new Registered($user));
//
        return $user;
    }

    protected function registered(Request $request, $user)
    {
        //
    }

    public function showRegistrationForm()
    {
        return view('backend.auth.register_dinetkan_new');
        
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // return redirect($this->redirectPath());
        
        return redirect()->to('auth')->with('success', 'Registrasi berhasil, silakan login..');

//        return redirect('/admin/account/get_info_dinetkan');
    }
    protected function guard()
    {
        return Auth::guard();
    }
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}

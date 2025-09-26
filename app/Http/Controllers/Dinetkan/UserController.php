<?php

namespace App\Http\Controllers\Dinetkan;

use App\Http\Requests\Owner\UserDinetkanRequest;
use App\Models\CountNumbering;
use App\Models\User;
use App\Models\Wablas;
use App\Models\Company;
use App\Models\License;
use App\Models\Midtrans;
use App\Models\TelegramBot;
use App\Models\PppoeSetting;
use Illuminate\Http\Request;
use App\Enums\UserStatusEnum;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
use App\Models\HotspotReseller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\DataTables\Owner\UserDataTable;
use App\Http\Requests\Owner\UserRequest;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable, Request $request)
    {
        $adminCount = User::where('role', 'Admin')->where('is_dinetkan',0)->orWhere('is_reguler',1)->count();
        $resellers = HotspotReseller::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->select('name', 'wa', 'id')
            ->get();
        $licenses = License::all();

        return $dataTable->render('owner.users', [
            'adminCount' => $adminCount,
            'resellers' => $resellers,
            'licenses' => $licenses,
        ]);
    }

    public function store(UserRequest $request)
    {
        $request->validated();

        try {
            $user = User::create([
                'id_group' => User::max('id_group') + 1,
                'shortname' => $request->username,
                'name' => $request->full_name,
                'role' => 'Admin',
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'license_id' => $request->license_id ?? 0,
                'next_due' => $request->next_due ? $request->next_due : null,
            ]);

            BillingSetting::create([
                'group_id' => $user->id_group,
                'due_bc' => 20,
                'inv_fd' => 1,
                'suspend_date' => 1,
                'suspend_time' => '06:00:00',
                'notif_bi' => 0,
                'notif_it' => 0,
                'notif_ps' => 0,
                'notif_sm' => 0,
            ]);

            Company::create([
                'group_id' => $user->id_group,
                'name' => $request->company_name,
                'nickname' => 'RADIUSQU',
                'email' => $request->email,
                'wa' => $request->whatsapp,
                'address' => 'Bandung, Jawa Barat, Indonesia',
                'logo' => 'favicon3.png',
            ]);

            PppoeSetting::create([
                'group_id' => $user->id_group,
                'shortname' => $user->shortname,
                'isolir' => 0,
                'type' => 'pppoe',
            ]);

            TelegramBot::create([
                'group_id' => $user->id_group,
                'chatid' => '-4798358286',
                'tipe' => 1,
            ]);

            TelegramBot::create([
                'group_id' => $user->id_group,
                'chatid' => '-4798358286',
                'tipe' => 2,
            ]);

            Wablas::create([
                'group_id' => $user->id_group,
                'sender' => '081222339257',
                'token' => '6WI9PiH8if9AkYZIhtvTbceaIjih0a',
            ]);

            WablasTemplate::create([
                'group_id' => $user->id_group,
                'invoice_terbit' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Invoice anda sudah terbit. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Bayar online klik [payment_midtrans] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>dash.radiusqu.com',
                'invoice_reminder' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Notifikasi ini hanya reminder untuk internet anda yang belum dibayar. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Jatuh Tempo : [jth_tempo]<br><br>Bayar online klik [payment_midtrans] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>dash.radiusqu.com',
                'invoice_overdue' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, Internet anda saat ini diisolir oleh *Bililng System* kami dikarenakan keterlambatan pembayaran.<br><br>Untuk dapat terus menikmati layanan internet kami, segera lakukan pembayaran senilai Rp. [total]<br><br>Bayar online klik [payment_midtrans] <br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>dash.radiusqu.com',
                'payment_paid' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Terimakasih, pembayaran internet anda telah kami terima. Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>No Invoice : [no_invoice]<br>Periode : [periode]<br>Paket Internet : [paket_internet]<br>Total : Rp. [total]<br>Status: *LUNAS*<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>dash.radiusqu.com',
                'payment_cancel' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Mohon maaf, pembayaran Internet Anda senilai *Rp. [total]* untuk periode *[periode]* telah kami batalkan, silakan hubungi admin untuk informasi lebih lanjut.<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>dash.radiusqu.com',
                'account_active' => 'Pelanggan Yth.<br>Bpk/Ibu *[nama_lengkap]*<br><br>Selamat internet anda sudah aktif! Berikut rinciannya<br><br>ID Pelanggan : [id_pelanggan]<br>Paket Internet : [paket_internet]<br>Harga : Rp. [harga]<br>Tipe Pembayaran : [tipe_pembayaran]<br>Jatuh Tempo : [jth_tempo]<br><br>Terimakasih telah mempercayakan pilihan internet anda kepada kami<br>dash.radiusqu.com',
                'account_suspend' => 'Sorry! Your Account Has Been Suspended',
            ]);

            Midtrans::create([
                'group_id' => $user->id_group,
            ]);

            event(new Registered($user));

            return response()->json(['message' => 'Admin created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating admin: ' . $e->getMessage()], 500);
        }
    }

    public function edit(User $user)
    {
        $user->company_name = $user->company->name;
        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $request->validated();

        try {
            $data = [
                'shortname' => $request->username,
                'name' => $request->full_name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'username' => $request->username,
                'license_id' => $request->license_id ?? 0,
                'next_due' => $request->next_due,
                'status' => $request->status,
            ];

            // Only update password if a new one is provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->filled('company_name')) {
                $company = Company::where('group_id', $user->id_group)->first();
                $company->name = $request->company_name;
                $company->save();
            }
            if($request->is_dinetkan == 1){
                $data['is_dinetkan'] = $request->is_dinetkan;
                $cekuser = User::where('id', $user->id)->first();
                if($cekuser->dinetkan_user_id == null || $cekuser->dinetkan_user_id == 0){
                    $userid = $this->generateMemberId();
                    $data['dinetkan_user_id'] = $userid;
                }
            }

            $user->update($data);

            return response()->json(['message' => 'Admin updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating admin: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->back()->with('success', 'Admin deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting admin: ' . $e->getMessage());
        }
    }

    public function status(User $user)
    {
        $user->status = $user->status === UserStatusEnum::ACTIVE ? UserStatusEnum::INACTIVE : UserStatusEnum::ACTIVE;
        $user->save();

        return response()->json(['message' => 'User status has been updated']);
    }

    public function loginAsUser(User $user)
    {
        session(['origin_id' => Auth::id()]);
        Auth::login($user);
        return redirect()->to('/')->with('success', 'You are now logged in as ' . $user->name);
    }

    public function logoutAsUser()
    {
        if (session()->has('origin_id')) {
            $adminId = session('origin_id');
            session()->forget('origin_id');

            Auth::loginUsingId($adminId);

            return redirect()->to('/')->with('success', 'You are now logged back in.');
        }

        return redirect()->route('login')->with('error', 'Unable to switch back.');
    }

    public function loginHistories($userId)
    {
        $user = User::findOrFail($userId);

        return DataTables::of($user->loginHistories()->getQuery())
            ->addIndexColumn()
            ->editColumn('login_at', function ($history) {
                return Carbon::parse($history->login_at)->format('d/m/Y H:i:s');
            })
            ->make(true);
    }

    public function generateMemberId()
    {
        // Get the latest member ID or start from 0
        $lastMember = CountNumbering::where('tipe', 'user_dinetkan')->first();
        $lastNumber = $lastMember ? (int)$lastMember->count : 0;
        $nextNumber = $lastNumber + 1;

        $lastMember->update(['count' => $nextNumber]);
        $userid = Carbon::now()->format('Ym').str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $userid;
    }
}

<?php


namespace App\Http\Controllers\Api\Sales;


use App\Models\AdminDinetkanInvoice;
use App\Models\CategoryLicenseDinetkan;
use App\Models\Company;
use App\Models\CountNumbering;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\Partnership\Mitra;
use App\Models\Province;
use App\Models\User;
use App\Models\UserDinetkan;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\DinetkanInvoiceStatusEnum;

class UserController
{

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = UserDinetkan::query()->where('id_mitra_sales', $request->user()->id_mitra);
        // 🔍 FILTER OPSIONAL
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }
        if ($request->filled('id_pelanggan')) {
            $query->where('dinetkan_user_id', 'like', '%' . $request->id_pelanggan . '%');
        }
        $transaksi = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($transaksi);
    }

    public function create()
    {
        $categories = CategoryLicenseDinetkan::get();
        $licenses = LicenseDinetkan::all();

        $provinces = [];
        $regencies = [];
        $districts = [];
        $villages = [];

        $provinces = Province::query()
            ->orderBy('name', 'asc')
            ->get();

        return view('backend.kemitraan.users.create', [
            'categories' => $categories,
            'licenses' => $licenses,
            'provinces' => $provinces,
            'regencies' => $regencies,
            'districts' => $districts,
            'villages' => $villages
        ]);
    }
    public function generateMemberId(): string
    {
        // Get the latest member ID or start from 0
        $prefix = \Illuminate\Support\Carbon::now()->format('Ym');
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
    protected function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }


    function create_users(Request $request){
        $password = $this->randomPassword();
        DB::beginTransaction();
        try {
            $users = User::query()->get();
            foreach ($users as $userx){
                if ($userx->email == trim($request->email) || $userx->username == trim($request->email) ) {
                    return response()->json(['message' => 'Data Email / Username sudah ada'], 500);
                }
                if (gantiformat_hp($userx->whatsapp) == gantiformat_hp(trim($request->whatsapp))) {
                    return response()->json(['message' => 'Data Whatsapp sudah ada'], 500);
                }

            }
            $user = UserDinetkan::create([
//                'id_group' => UserDinetkan::max('id_group') + 1,
                'shortname' => Str::trim($request->first_name) . Str::trim($request->last_name),
                'name' => $request->first_name . " " . $request->last_name,
                'role' => 'Admin',
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'username' => $request->email,
                'password' => Hash::make($password),
                'status' => $request->status,
                'license_id' => 2,
                'next_due' => '9999-12-31', //$request->next_due ? $request->next_due : null,
                'vlan' => $request->vlan,
                'metro' => $request->metro,
                'vendor' => $request->vendor,
                'trafic_mrtg' => $request->trafic_mrtg,
                'ip_prefix' => $request->ip_prefix,
                'is_dinetkan' => 1,
                'otc_license_dinetkan_id' => $request->otc_license_dinetkan_id,
                'mrc_license_dinetkan_id' => $request->mrc_license_dinetkan_id,
                'dinetkan_user_id' => $this->generateMemberId(),

                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
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
                'id_mitra_sales' => $request->user()->id_mitra,
                'created_by' => $request->user()->id_mitra
            ]);
            Company::create([
                'group_id' => $user->id,
                'name' => $request->company_name,
                'nickname' => 'RADIUSQU',
                'email' => $request->email,
                'wa' => $request->whatsapp,
                'address' => 'Bandung, Jawa Barat, Indonesia',
                'logo' => 'favicon3.png',
            ]);
            DB::commit();
            return response()->json(['message' => 'User created successfully'], 201);
        } catch (UniqueConstraintViolationException $e) {
            DB::rollBack();
            $fullMessage = $e->getMessage();

            // Ambil hanya bagian error sebelum `(Connection:` jika ada
            if (preg_match('/^(SQLSTATE\[23000\].*?)( \(Connection:.*)?$/', $fullMessage, $matches)) {
                $cleanMessage = $matches[1]; // hanya bagian SQLSTATE... Duplicate entry ...
            } else {
                $cleanMessage = $fullMessage; // fallback kalau regex gagal
            }

            return response()->json([
                'message' => 'Data sudah ada (duplikat): ' . $cleanMessage
            ], 409);
        } catch (QueryException $e) {
            DB::rollBack();
            // Tangani kesalahan query umum (misal kolom tidak ditemukan, syntax error)
            return response()->json([
                'message' => 'Kesalahan Query Database: ' . $e->getMessage()
            ], 500);
        } catch (PDOException $e) {
            DB::rollBack();
            // Tangani error dari PDO (biasanya koneksi DB atau masalah level rendah)
            return response()->json([
                'message' => 'Kesalahan PDO: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            // Tangani semua error tak terduga lainnya
            return response()->json([
                'message' => 'Kesalahan Umum: ' . $e->getMessage()
            ], 500);
        }
    }

    public function keuangan_dinetkan(Request $request)
    {
        if (multi_auth()->role !== 'Mitra') {
            $incomeMonth = TransaksiMitra::where('shortname', $request->user()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->month)
                ->sum('nominal');
            $incomeLastMonth = TransaksiMitra::where('shortname', $request->user()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->subMonth()->month)
                ->sum('nominal');
            $incomeYear = TransaksiMitra::where('shortname', $request->user()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->whereYear('tanggal', Carbon::today()->year)
                ->sum('nominal');
            $totalmitra = Mitra::where('shortname', $request->user()->shortname)->count();
        } else {
            $incomeMonth = TransaksiMitra::where('shortname', $request->user()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->where('mitra_id', $request->user()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->month)
                ->sum('nominal');
            $incomeLastMonth = TransaksiMitra::where('shortname', $request->user()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->where('mitra_id', $request->user()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->subMonth()->month)
                ->sum('nominal');
            $incomeYear = TransaksiMitra::where('shortname', $request->user()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->where('mitra_id', $request->user()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->sum('nominal');
            $totalmitra = 0;
        }

        $perPage = $request->get('per_page', 10); // default 10 item per halaman

        $query = TransaksiMitra::query()
            ->where('shortname', $request->user()->shortname)
            ->where('mitra_id', $request->user()->id)
            ->with('mitra')
            ->where('is_dinetkan', 1);
        // 🔍 FILTER OPSIONAL
//        if ($request->filled('first_name')) {
//            $query->where('first_name', 'like', '%' . $request->first_name . '%');
//        }
//        if ($request->filled('id_pelanggan')) {
//            $query->where('dinetkan_user_id', 'like', '%' . $request->id_pelanggan . '%');
//        }
        $transaksi = $query->orderBy('id', 'desc')->paginate($perPage);
        $response = array(
            'incomeMonth' => $incomeMonth,
            'incomeLastMonth' => $incomeLastMonth,
            'incomeYear' => $incomeYear,
            'transaksi' => $transaksi
        );
        return response()->json($response);
    }

    function validate_data(Request $request)
    {
        $users = User::query()->get();
        foreach ($users as $user){
            if ($request->field == 'email') {
                if ($user->email == trim($request->field_data) || $user->username == trim($request->field_data)) {
                    return response()->json(['message' => 'Email / Username sudah ada'], 500);
                }
            }
            if ($request->field == 'whatsapp') {
                if (gantiformat_hp($user->whatsapp) == gantiformat_hp(trim($request->field_data))) {
                    return response()->json(['message' => 'Data Whatsapp sudah ada'], 500);
                }
            }
        }
        return response()->json(['message' => 'Data aman'], 201);
    }

    public function invoice(){
        $licenses = LicenseDinetkan::all();
        $categories = CategoryLicenseDinetkan::get();
        $resellers = UserDinetkan::where('is_dinetkan',1)
            ->where('id_mitra_sales', $request->user()->id_mitra)
            ->get();
        return view('backend.kemitraan.invoice.index',
            compact(
                'licenses',
                'categories','resellers'
            ));
    }


    public function unpaid(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman

        $query = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
            ->where('due_date','>=', \Illuminate\Support\Carbon::now())
            ->whereHas('mapping_mitra')
            ->with('mapping_mitra');
        // 🔍 FILTER OPSIONAL
//        if ($request->filled('first_name')) {
//            $query->where('first_name', 'like', '%' . $request->first_name . '%');
//        }
//        if ($request->filled('id_pelanggan')) {
//            $query->where('dinetkan_user_id', 'like', '%' . $request->id_pelanggan . '%');
//        }
        $transaksi = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($transaksi);
    }



    public function paid(Request $request)
    {

        $perPage = $request->get('per_page', 10); // default 10 item per halaman

        $query = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::PAID)
            ->where('due_date','>=', \Illuminate\Support\Carbon::now())
            ->whereHas('mapping_mitra')
            ->with('mapping_mitra');
        // 🔍 FILTER OPSIONAL
//        if ($request->filled('first_name')) {
//            $query->where('first_name', 'like', '%' . $request->first_name . '%');
//        }
//        if ($request->filled('id_pelanggan')) {
//            $query->where('dinetkan_user_id', 'like', '%' . $request->id_pelanggan . '%');
//        }
        $transaksi = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($transaksi);
    }


    public function expired(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman

        $query = AdminDinetkanInvoice::query()->whereIn('status', [DinetkanInvoiceStatusEnum::EXPIRED,DinetkanInvoiceStatusEnum::CANCEL])
            ->where('due_date','>=', \Illuminate\Support\Carbon::now())
            ->whereHas('mapping_mitra')
            ->with('mapping_mitra');
        // 🔍 FILTER OPSIONAL
//        if ($request->filled('first_name')) {
//            $query->where('first_name', 'like', '%' . $request->first_name . '%');
//        }
//        if ($request->filled('id_pelanggan')) {
//            $query->where('dinetkan_user_id', 'like', '%' . $request->id_pelanggan . '%');
//        }
        $transaksi = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($transaksi);

    }
}

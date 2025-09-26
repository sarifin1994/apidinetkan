<?php

namespace App\Http\Controllers\Dinetkan;

use App\DataTables\Owner\UserDinetkanDataTable;
use App\Http\Requests\Dinetkan\UserDinetkanRequest;
use App\Mail\TestEmail;
use App\Models\CategoryLicenseDinetkan;
use App\Models\CountNumbering;
use App\Models\Districts;
use App\Models\DocType;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\MasterMetro;
use App\Models\MasterPop;
use App\Models\Province;
use App\Models\Regencies;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Models\UserDinetkanGraph;
use App\Models\UserDoc;
use App\Models\UsersWhatsapp;
use App\Models\UserWhatsappGroup;
use App\Models\Villages;
use App\Models\Wablas;
use App\Models\Company;
use App\Models\License;
use App\Models\Midtrans;
use App\Models\TelegramBot;
use App\Models\PppoeSetting;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use App\Enums\UserStatusEnum;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Support\Carbon;
//use App\Models\HotspotReseller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\DataTables\Owner\UserDataTable;
use App\Http\Requests\Owner\UserRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Services\AdminDinetkanPaymentService;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\ServiceStatusEnum;

class UserDinetkanController extends Controller
{
    public function __construct(
        private LicenseDinetkanRepositoryInterface $licenseDinetkanRepo,
        private AdminDinetkanInvoiceRepositoryInterface $adminDinetkanInvoiceRepo,
        private AdminDinetkanPaymentService $adminDinetkanPaymentService
    ) {
    }

public
function index(UserDinetkanDataTable $dataTable, Request $request)
{
    $adminCount = UserDinetkan::where('role', 'Admin')->where('is_dinetkan', 1)->count();
    $categories = CategoryLicenseDinetkan::get();
//        $resellers = HotspotReseller::where('group_id', $request->user()->id_group)
//            ->where('status', 1)
//            ->select('name', 'wa', 'id')
//            ->get();
    $licenses = LicenseDinetkan::all();
    $licensesotc = LicenseDinetkan::where('type', 'otc')->get();
    $licensesmrc = LicenseDinetkan::where('type', 'mrc')->get();

    $provinces = [];
    $regencies = [];
    $districts = [];
    $villages = [];

    $provinces = Province::query()
        ->orderBy('name', 'asc')
        ->get();
//        $regencies = Regencies::query()
//        ->orderBy('name', 'asc')
//        ->get();
//        $districts = Districts::query()
//        ->orderBy('name', 'asc')
//        ->get();
//        $villages = Villages::query()
//        ->orderBy('name', 'asc')
//        ->get();

    return $dataTable->render('backend.dinetkan.users_dinetkan', [
        'adminCount' => $adminCount,
//            'resellers' => $resellers,
        'licensesotc' => $licensesotc,
        'licensesmrc' => $licensesmrc,
        'categories' => $categories,
        'licenses' => $licenses,
        'provinces' => $provinces,
        'regencies' => $regencies,
        'districts' => $districts,
        'villages' => $villages
    ]);
}

protected
function randomPassword()
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

public function generateMemberId(): string
{
    // Get the latest member ID or start from 0
    $prefix = Carbon::now()->format('Ym');
    $lastMember = CountNumbering::where('tipe', 'user_dinetkan')
        ->first();
//        $lastMember = CountNumbering::where('tipe', 'user_dinetkan')->where('prefix', $prefix)
//            ->first();
//        if($lastMember == null){
//            CountNumbering::create([
//                'tipe' => 'user_dinetkan',
//                'count' => 0,
//                'prefix' => $prefix
//            ]);
//            $lastMember = CountNumbering::where('tipe', 'user_dinetkan')->where('prefix', $prefix)
//                ->first();
//        }
    $lastNumber = $lastMember ? (int)$lastMember->count : 0;
    $nextNumber = $lastNumber + 1;

    // Format to 8 digits with leading zeros

    $lastMember->count = $nextNumber;
    $lastMember->save();
    $userid = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    return $userid;
}

public function store(UserDinetkanRequest $request)
{
    $request->validated();
    $password = $this->randomPassword();
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
            'status' => 1, //$request->status,
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
            'address' => $request->address
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


        $details = [
            'email' => $request->email,
            'subject' => 'Registrasi',
            'username' => $request->email,
            'password' => $password
        ];

        Mail::to($details['email'])->send(new TestEmail($details));
        try {
            $message_format = "Registrasi Berhasil!
            
Selamat, akun Anda telah berhasil didaftarkan. Berikut adalah informasi akun Anda:
Username : $request->email
Password : $password
Silakan gunakan informasi ini untuk masuk ke akun Anda.
Jika Anda tidak merasa mendaftar, harap abaikan email ini atau hubungi tim dukungan kami.



PT Putra Garsel Interkoneksi
Jalan Asia-Afrika No.114-119 Wisma Bumi Putera Lt.3 Suite .301 B
Kb. Pisang, Kec. Sumur Bandung, Kota Bandung, Jawa Barat 40112
+62 822-473-377";
            $user_dinetkan = User::where('shortname', 'dinetkan')->first();
            $mpwa = UsersWhatsapp::where('user_id', $user_dinetkan->id)->first();
            if($mpwa){
                $nomorhp = gantiformat_hp($request->whatsapp);
                $_id = $user_dinetkan->whatsapp."_".env('APP_ENV');
                $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                $params = array(
                    "jid" => $nomorhp."@s.whatsapp.net",
                    "content" => array(
                        "text" => $message_format
                    )
                );

                $response = Http::timeout(10)->post($apiUrl, $params);
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return response()->json(['message' => 'Admin created successfully'], 201);
    } catch (UniqueConstraintViolationException $e) {
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
        // Tangani kesalahan query umum (misal kolom tidak ditemukan, syntax error)
        return response()->json([
            'message' => 'Kesalahan Query Database: ' . $e->getMessage()
        ], 500);
    } catch (PDOException $e) {
        // Tangani error dari PDO (biasanya koneksi DB atau masalah level rendah)
        return response()->json([
            'message' => 'Kesalahan PDO: ' . $e->getMessage()
        ], 500);
    } catch (Exception $e) {
        // Tangani semua error tak terduga lainnya
        return response()->json([
            'message' => 'Kesalahan Umum: ' . $e->getMessage()
        ], 500);
    }
}

public
function placeOrder(UserDinetkan $user, int $licenseId)
{
    /** @var User $user */
//        $user = $request->user();
    $license = $this->licenseDinetkanRepo->findById($licenseId);
    if ($license) {
        $this->adminDinetkanPaymentService->createLicenseInvoice($license, $user, '');
    }
}

public
function edit(UserDinetkan $user)
{
    $user->company_name = $user->company->name;
    return response()->json($user);
}

public
function update(UserDinetkanRequest $request, UserDinetkan $user)
{
    $request->validated();

    try {
        $data = [
//                'shortname' => $request->first_name." ".$request->last_name,
            'name' => $request->first_name . " " . $request->last_name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'username' => $request->email,
            'license_id' => $request->license_id ?? 0,
//                'next_due' => '9999-12-31', //$request->next_due,
            'status' => $request->status,
            'vlan' => $request->vlan,
            'metro' => $request->metro,
            'vendor' => $request->vendor,
            'trafic_mrtg' => $request->trafic_mrtg,
            'ip_prefix' => $request->ip_prefix,
            'otc_license_dinetkan_id' => $request->otc_license_dinetkan_id,
            'mrc_license_dinetkan_id' => $request->mrc_license_dinetkan_id,

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

        $user->update($data);

        return response()->json(['message' => 'Admin updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error updating admin: ' . $e->getMessage()], 500);
    }
}


public
function update_new(Request $request)
{
    try {
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();

        $users = User::query()->get();
        foreach ($users as $user){
            if ($user->dinetkan_user_id != $request->dinetkan_user_id && gantiformat_hp($user->whatsapp) == gantiformat_hp(trim($request->whatsapp))) {
                return response()->json(['message' => 'Data Whatsapp sudah ada'], 500);
            }

        }

        $data = array(
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'whatsapp' => $request->whatsapp,
            'id_card' => $request->id_card,
            'npwp' => $request->npwp,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id,
            'address' => $request->address
        );

        $cekcompany = Company::where('group_id', $userdinetkan->id)->first();
        if (!$cekcompany) {
            Company::create([
                'group_id' => $userdinetkan->id,
                'name' => $request->company_name,
                'nickname' => 'RADIUSQU',
                'email' => $userdinetkan->email,
                'wa' => $userdinetkan->whatsapp,
//                    'address' => 'Bandung, Jawa Barat, Indonesia',
//                    'logo' => 'favicon3.png',
            ]);
        }
//            echo "<br>";
//            echo json_encode($data);exit;

        $userdinetkan->update($data);
        return redirect()->back()->with('success', 'Mitra updated successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Mitra dinetkan updated failed');
    }
}


public
function update_cacti(Request $request)
{
    try {
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $metro = MasterMetro::where('id', $request->metro_id)->first();
        $group_name = "";
        $wag = UserWhatsappGroup::where('group_id', $request->id_wag)->first();
        if ($wag) {
            $group_name = $wag->group_name;
        }
        $data = array(
            'vlan' => $request->vlan,
            'metro' => $metro->name,
            'metro_id' => $metro->id,
            'vendor' => $request->vendor,
            'trafic_mrtg' => $request->trafic_mrtg,
            'ip_prefix' => $request->ip_prefix,
            'pop_id' => $request->pop,
            'group_id' => $request->id_wag,
            'group_name' => $group_name,
        );
        $userdinetkan->update($data);

        return redirect()->back()->with('success', 'Mitra dinetkan updated successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error updating mitra dinetkan: ' . $e->getMessage());
    }
}

public
function update_cacti2(Request $request)
{
    try {
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id', $userdinetkan->dinetkan_user_id)
            ->where('graph_id', $request->trafic_mrtg_graph)->first();
        $datagraph = array(
            'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
            'graph_name' => $request->graph_name,
            'graph_id' => $request->trafic_mrtg_graph,
            'pop_id' => $request->pop
        );
        if ($userdinetkanGraph) {
            $userdinetkanGraph->update($datagraph);
        }
        if (!$userdinetkanGraph) {
            UserDinetkanGraph::create($datagraph);
        }

        return response()->json(['message' => 'Mitra dinetkan updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error updating mitra dinetkan: ' . $e->getMessage()], 500);
    }
}

public
function update_cacti_service(Request $request)
{
    try {
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id', $userdinetkan->dinetkan_user_id)
            ->where('graph_id', $request->trafic_mrtg_graph)->first();
        $datagraph = array(
            'dinetkan_user_id' => $userdinetkan->dinetkan_user_id,
            'graph_name' => $request->graph_name,
            'graph_id' => $request->trafic_mrtg_graph,
            'pop_id' => $request->pop,
            'service_id' => $request->service_id,
        );
        if ($userdinetkanGraph) {
            $userdinetkanGraph->update($datagraph);
        }
        if (!$userdinetkanGraph) {
            UserDinetkanGraph::create($datagraph);
        }

        return response()->json(['message' => 'Mitra dinetkan updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error updating mitra dinetkan: ' . $e->getMessage()], 500);
    }
}

public
function destroy(UserDinetkan $user)
{
    try {
        $user->delete();
        return redirect()->back()->with('success', 'Admin deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting admin: ' . $e->getMessage());
    }
}

public
function status(UserDinetkan $user)
{
    $user->status = $user->status === UserStatusEnum::ACTIVE ? UserStatusEnum::INACTIVE : UserStatusEnum::ACTIVE;
    $user->save();

    return response()->json(['message' => 'User status has been updated']);
}

public
function loginAsUser(UserDinetkan $user)
{
    session(['origin_id' => Auth::id()]);
    Auth::login($user);
    return redirect()->to('/')->with('success', 'You are now logged in as ' . $user->name);
}

public
function logoutAsUser()
{
    if (session()->has('origin_id')) {
        $adminId = session('origin_id');
        session()->forget('origin_id');

        Auth::loginUsingId($adminId);

        return redirect()->to('/')->with('success', 'You are now logged back in.');
    }

    return redirect()->route('login')->with('error', 'Unable to switch back.');
}

public
function loginHistories($userId)
{
    $user = UserDinetkan::findOrFail($userId);

    return DataTables::of($user->loginHistories()->getQuery())
        ->addIndexColumn()
        ->editColumn('login_at', function ($history) {
            return Carbon::parse($history->login_at)->format('d/m/Y H:i:s');
        })
        ->make(true);
}

protected
function cacti_login()
{
    $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
    $apiUrl = env('CACTI_ENDPOINT') . 'cacti/login/' . $_id;
    try {
        $params = array(
            "action" => "login",
            "login_username" => "wijaya",
            "login_password" => "wijaya@2024"
        );
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->post($apiUrl, $params);
        Storage::disk('local')->append('owner_cacti.txt', json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n");
        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data['success'] ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

protected
function get_tree_mrtg()
{
    try {
        $params = array(
            "action" => "get_node",
            "tree_id" => "0",
            "id" => "%23"
        );
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT') . 'cacti/graph_view/' . $_id . '?' . urldecode(http_build_query($params));
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->get($apiUrl);
        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

public
function get_tree_node_mrtg($id)
{
    // step 2
    try {
        $params = array(
            "action" => "get_node",
            "tree_id" => "0",
            "id" => $id
        );
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT') . '/cacti/graph_view/' . $_id . '?' . urldecode(http_build_query($params));
        // Kirim POST request ke API eksternal
        $response = Http::get($apiUrl);

        // Periksa apakah request berhasil
        //            print_r($response->json());exit;
        if ($response->successful()) {
            $data = $response->json();
            return $data ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}


public
function get_graph_mrtg($id, $page = 1)
{
    // step 2
    try {
        $params = array(
            'action' => 'tree_content',
            'node' => $id,
            'tree_id' => 2,
            'leaf_id' => 15,
            'hgd' => '',
            'header' => false,
            'page' => $page
        );
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT') . 'cacti/v2/graph_view/' . $_id . '?' . urldecode(http_build_query($params));
        // Kirim POST request ke API eksternal
        $response = Http::get($apiUrl);

        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = collect($response->json())->map(function ($datax) {
                return [
                    'rra_id' => $datax['rra_id'],
                    'local_graph_id' => $datax['local_graph_id'],
                    'graph_start' => $datax['graph_start'],
                    'graph_end' => $datax['graph_end'],
                    'graph_height' => $datax['graph_height'],
                    'graph_width' => $datax['graph_width'],
                    'Title' => $datax['GraphXport'][0]['Title'],

                ];
            });
            //                print_r($data);exit;
            return $data ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

public
function get_graph_mrtgX($node)
{
    try {
        $params = array(
            'action' => 'tree_content',
            'node' => $node,
            'tree_id' => '2',
            'leaf_id' => '15',
            'hgd' => '',
            'header' => false
        );
        $apiUrl = 'http://103.184.122.170/api/cacti/graph_view?' . urldecode(http_build_query($params));
        // Kirim POST request ke API eksternal
        $response = Http::get($apiUrl);

        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

protected
function cacti_logout()
{
    //        http://103.184.122.170/api/cacti/logout/:_id
    $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
    $apiUrl = env('CACTI_ENDPOINT') . 'cacti/logout/' . $_id;
    try {
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->get($apiUrl);
        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data['success'] ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

public
function detail($dinetkan_user_id)
{
    $this->cacti_logout();
    $userdinetkan = UserDinetkan::where('dinetkan_user_id', $dinetkan_user_id)->first();
    $userdinetkanGraph = null;
    if ($userdinetkan) {
        $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id', $userdinetkan->dinetkan_user_id)->get();
    }
    $pop = MasterPop::get();
    $login = $this->cacti_login();
    $tree = $this->get_tree_mrtg();
    $metro = MasterMetro::all();
    $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
    $maapingUserLicense = MappingUserLicense::where('dinetkan_user_id', $dinetkan_user_id)->first();
//        if(!$maapingUserLicense){
//            return redirect()->back()->with('error', 'Member ini belum ada order yang aktif');
//        }
    $status_id = $maapingUserLicense ? $maapingUserLicense->status->value : 0;
    $status = "inactive";
    if ($maapingUserLicense) {
        if ($maapingUserLicense->status == ServiceStatusEnum::ACTIVE->value){
            $status = "active";
        }
        }

    $provinces = Province::query()
        ->orderBy('name', 'asc')
        ->get();
    $regencies = [];
    if ($userdinetkan->province_id) {
        $regencies = Regencies::where('province_id', $userdinetkan->province_id)->get();
    }
    $districts = [];
    if ($userdinetkan->regency_id) {
        $districts = Districts::where('regency_id', $userdinetkan->regency_id)->get();
    }
    $villages = [];
    if ($userdinetkan->district_id) {
        $villages = Villages::where('district_id', $userdinetkan->district_id)->get();
    }
    $docType = DocType::all();
    $listDoc = UserDoc::with('docType')->where('user_id', $userdinetkan->id)->get();
    return view('backend.dinetkan.users_dinetkan_detail',
        compact(
            'userdinetkan',
            'userdinetkanGraph',
            'tree',
            'pop',
            'metro',
            'wag',
            'maapingUserLicense',
            'status',
            'status_id',
            'docType',
            'listDoc', 'provinces', 'regencies', 'districts', 'villages'
        ));
}

public
function detail_cacti($dinetkan_user_id)
{
    $this->cacti_logout();
    $userdinetkan = UserDinetkan::where('dinetkan_user_id', $dinetkan_user_id)->first();
    $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id', $userdinetkan->dinetkan_user_id)->get();
    $pop = MasterPop::get();
    $login = $this->cacti_login();
    $tree = $this->get_tree_mrtg();
    $metro = MasterMetro::all();
    $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
    $maapingUserLicense = MappingUserLicense::where('dinetkan_user_id', $dinetkan_user_id)->first();
//        if(!$maapingUserLicense){
//            return redirect()->back()->with('error', 'Member ini belum ada order yang aktif');
//        }
    $status = "inactive";
    if (isset($maapingUserLicense->status)) {
        if ($maapingUserLicense->status == ServiceStatusEnum::ACTIVE) {
            $status = "active";
        }
    }
    return view('backend.dinetkan.users_dinetkan_cacti',
        compact(
            'userdinetkan',
            'userdinetkanGraph',
            'tree',
            'pop',
            'metro',
            'wag',
            'maapingUserLicense',
            'status'
        ));
}

public
function single_cacti($id)
{
    $cacti = UserDinetkanGraph::where('id', $id)->first();
    return response()->json($cacti);
}

public
function delete_cacti($id)
{
    try {
        $cacti = UserDinetkanGraph::where('id', $id)->first();
        $cacti->delete();
        return redirect()->back()->with('success', 'Data deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting data: ' . $e->getMessage());
    }
}

public
function service_by_mitra($dinetkan_user_id)
{
    $mapping = DB::table('member_dinetkan')
        ->select('member_dinetkan.*', 'users.first_name as fmitra', 'users.last_name as lmitra')
        ->join('product_dinetkan', 'member_dinetkan.product_dinetkan_id', '=', 'product_dinetkan.id')
        ->join('users', 'users.dinetkan_user_id', '=', 'member_dinetkan.dinetkan_user_id')
        ->where('member_dinetkan.dinetkan_user_id', $dinetkan_user_id)->get();
    return view('backend.dinetkan.service_dinetkan_by_mitra', compact('mapping'));
}

//public
//function update_doc_info_dinetkan(Request $request)
//{
//    $user = UserDinetkan::where('dinetkan_user_id', $request->doc_dinetkan_user_id)->first();
//    $request->validate([
//        'doc' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:2048',
//        'doc_id' => 'required'
//    ]);
//
//    $file = $request->file('doc');
//    // Tentukan folder penyimpanan berdasarkan jenis file
//    $folder = 'user_document'; //$file->getClientOriginalExtension() == 'pdf' ? 'documents' : 'images';
//
//    // Buat nama file unik
//    $service_id = "";
//    if ($request->service_id) {
//        $service_id = $request->service_id . '-';
//    }
//    $customName = $service_id . $user->id . '_' . $request->doc_id . '.' . $file->getClientOriginalExtension();
//
//    // Simpan file ke storage/app/public/images atau storage/app/public/documents
//    $path = $file->storeAs($folder, $customName, 'local');
//
//    // Simpan ke database
//    if ($request->service_id) {
//
//        $fileUpload = new UserDoc();
//        $fileUpload->file_name = $customName;
//        $fileUpload->doc_id = $request->doc_id;
//        $fileUpload->service_id = $request->service_id;
//        $fileUpload->user_id = $user->id;
//        $fileUpload->file_ext = $file->getClientOriginalExtension();
//        $fileUpload->path = $path;
//        $fileUpload->save();
//
//        return redirect()->back()->with('success', 'Document updated successfully');
//    }
//    $fileUpload = new UserDoc();
//    $fileUpload->file_name = $customName;
//    $fileUpload->doc_id = $request->doc_id;
//    $fileUpload->user_id = $user->id;
//    $fileUpload->file_ext = $file->getClientOriginalExtension();
//    $fileUpload->path = $path;
//    $fileUpload->save();
//
//    return redirect()->back()->with('success', 'Document updated successfully');
//}

public function update_doc_info_dinetkan(Request $request)
{
    try {
        // Cari user berdasarkan dinetkan_user_id
        $user = UserDinetkan::where('dinetkan_user_id', $request->doc_dinetkan_user_id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validasi input
        $request->validate([
            'doc' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:5120',
            'doc_id' => 'required'
        ]);

        $file = $request->file('doc');

        // Folder penyimpanan
        $folder = 'user_document';

        // Buat nama file unik
        $service_id = $request->service_id ? $request->service_id . '-' : '';
        $customName = $service_id . $user->id . '_' . $request->doc_id . '.' . $file->getClientOriginalExtension();

        // Simpan file
        $path = $file->storeAs($folder, $customName, 'local');

        // Simpan ke database
        $fileUpload = new UserDoc();
        $fileUpload->file_name = $customName;
        $fileUpload->doc_id = $request->doc_id;
        $fileUpload->user_id = $user->id;
        $fileUpload->file_ext = $file->getClientOriginalExtension();
        $fileUpload->path = $path;
        if ($request->service_id) {
            $fileUpload->service_id = $request->service_id;
        }
        $fileUpload->save();

        return response()->json(['message' => 'Document updated successfully'], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => "Upload document => ".$e->errors()['doc'][0] ?? 'Validasi gagal'
        ], 500);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Document update failed: ' . $e->getMessage()], 500);
    }
}

public
function show_file($id)
{
    if (!multi_auth()) {
        abort(403, 'Anda tidak memiliki izin untuk mengakses file ini.');
    }

    $userDoc = UserDoc::where('id', $id)->first();
    //        echo $userDoc->path;exit;
    $path = storage_path('app/private/' . $userDoc->path);

    if (!file_exists($path)) {
        abort(404, 'File tidak ditemukan.');
    }

    return response()->file($path);
}

public
function detail2($dinetkan_user_id)
{
    $this->cacti_logout();
    $userdinetkan = UserDinetkan::where('dinetkan_user_id', $dinetkan_user_id)->first();
    $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id', $userdinetkan->dinetkan_user_id)->get();
    $pop = MasterPop::get();
    $login = $this->cacti_login();
    $tree = $this->get_tree_mrtg();
    $metro = MasterMetro::all();
    $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
    $maapingUserLicense = MappingUserLicense::where('dinetkan_user_id', $dinetkan_user_id)->first();
    //        if(!$maapingUserLicense){
    //            return redirect()->back()->with('error', 'Member ini belum ada order yang aktif');
    //        }
    $status_id = $maapingUserLicense ? $maapingUserLicense->status : 0;
    $status = "inactive";
    if ($maapingUserLicense) {
        if ($maapingUserLicense->status == ServiceStatusEnum::ACTIVE) {
            $status = "active";
        }
    }
    $provinces = Province::query()
        ->orderBy('name', 'asc')
        ->get();
    $regencies = [];
    if ($userdinetkan->province_id) {
        $regencies = Regencies::where('province_id', $userdinetkan->province_id)->get();
    }
    $districts = [];
    if ($userdinetkan->regency_id) {
        $districts = Districts::where('regency_id', $userdinetkan->regency_id)->get();
    }
    $villages = [];
    if ($userdinetkan->district_id) {
        $villages = Villages::where('district_id', $userdinetkan->district_id)->get();
    }
    $docType = DocType::all();
    $listDoc = UserDoc::with('docType')->where('user_id', $userdinetkan->id)->get();
    return view('backend.dinetkan.users_dinetkan_detail_2',
        compact(
            'userdinetkan',
            'userdinetkanGraph',
            'tree',
            'pop',
            'metro',
            'wag',
            'maapingUserLicense',
            'status',
            'status_id',
            'docType',
            'listDoc', 'provinces', 'regencies', 'districts', 'villages'
        ));
}

public function accept(Request $request)
{
    try {
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $password = $this->randomPassword();
        $data = array(
            'status' => 1,
            'password' => Hash::make($password),
        );
        $details = [
            'email' => $userdinetkan->email,
            'subject' => 'Registrasi',
            'username' => $userdinetkan->email,
            'password' => $password
        ];
        Mail::to($details['email'])->send(new TestEmail($details));
        $userdinetkan->update($data);
        return redirect()->route('dinetkan.invoice_dinetkan.order')->with('success', 'Accept sucessfully');
    } catch (\Exception $e) {
        return redirect()->route('dinetkan.invoice_dinetkan.order')->with('success', 'Accept failed');
    }
}

public function accept_new(Request $request)
{
    try {
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $request->dinetkan_user_id)->first();
        $password = $this->randomPassword();
        $data = array(
            'status' => 1,
            'password' => Hash::make($password),
        );
        $details = [
            'email' => $userdinetkan->email,
            'subject' => 'Registrasi',
            'username' => $userdinetkan->email,
            'password' => $password
        ];
        Mail::to($details['email'])->send(new TestEmail($details));
        $userdinetkan->update($data);
        return response()->json(['message' => 'Admin created successfully'],201);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Admin created failed '.$e->getMessage()],500);
    }
}

public
function service(Request $request)
{
    $mapping = DB::table('billing_service')
        ->select('billing_service.*', 'users.name as nama_mitra')
        ->join('users', 'users.dinetkan_user_id', '=', 'billing_service.dinetkan_user_id')->get();
    return view('backend.dinetkan.service_dinetkan', compact('mapping'));
}

public
function get_admin_import()
{
    $admin = User::where('role', 'Admin')
        ->where('is_dinetkan', 0)
        ->whereNull('ext_role')->get();
    return response()->json($admin);
}

public
function import(Request $request)
{
    try {
        $import_admin = $request->import_admin;
        if (count($import_admin) > 0) {
            foreach ($import_admin as $import) {
                $user = User::where('id', $import)->first();
                if ($user) {
                    $dinetkan_user_id = $this->generateMemberId();
                    if ($user->license_id <= 2) {
                        $data_update = [
                            'license_id' => 2,
                            'next_due' => '9999-12-31'
                        ];
                        $user->update($data_update);
                    }
                    $data_update = [
                        'is_dinetkan' => 1,
                        'is_import' => 1,
                        'dinetkan_user_id' => $dinetkan_user_id
                    ];
                    $user->update($data_update);
                    $details = [
                        'email' => $user->email,
                        'subject' => 'Registrasi Mitra',
                        'username' => $user->email,
                        'password' => "Sesuai Dengan Password yang aktif"
                    ];
                    Mail::to($details['email'])->send(new TestEmail($details));
                    return response()->json(
                        [
                            'message' => 'Admin created successfully',
                            'redirect' => route('dinetkan.users_dinetkan.detail', $dinetkan_user_id)]
                        , 201);
//                        return redirect()->route('dinetkan.users_dinetkan.detail', $dinetkan_user_id)->with('success', 'Accept sucessfully');
                }
            }
        }
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error creating admin: ' . $e->getMessage()], 500);
    }
}

public
function delete($id)
{
    try {
        $userdinetkan = User::where('id', $id)->first();
//            echo ($userdinetkan->is_import);exit;
        if ($userdinetkan) {
            if ($userdinetkan->is_import == 1) {
                $data_update = [
                    'next_due' => Carbon::today()->addDays(30)->format('Y-m-d'),
                    'is_dinetkan' => 0,
                    'is_import' => 0,
                    'dinetkan_user_id' => ''
                ];
                $userdinetkan->update($data_update);
                return redirect()->back()->with('success', 'Admin deleted successfully');
            }
            if ($userdinetkan->is_import == 0) {
                $userdinetkan->delete();
                return redirect()->back()->with('success', 'Admin deleted successfully');
            }
        }

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting admin: ' . $e->getMessage());
    }
}

public
function get_single($id)
{
    $user = User::where('dinetkan_user_id', $id)->first();
    return response()->json($user);
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

    return view('backend.dinetkan.users_dinetkan_create', [
        'categories' => $categories,
        'licenses' => $licenses,
        'provinces' => $provinces,
        'regencies' => $regencies,
        'districts' => $districts,
        'villages' => $villages
    ]);
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
public function getdata(Request $request){
        if($request->ajax()){

            $users = UserDinetkan::query()
                ->with('company')
                ->where('role', 'Admin')
                ->where('is_dinetkan', 1)
                ->where('status', $request->status);
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if($row->status != 4) {
                        $buttons = '<a href="' . route('dinetkan.users.login-as', $row->id) . '" class="btn btn-light-primary icon-btn b-r-4"><span class="material-symbols-outlined">input</span></a>';

                        $buttons .= '<a href="' . route('dinetkan.users_dinetkan.detail', $row->dinetkan_user_id) . '" class="btn btn-light-success icon-btn b-r-4" ><span class="material-symbols-outlined">visibility</span></a>';
                        $buttons .= '<a href="' . route('dinetkan.users_dinetkan.detail_cacti', $row->dinetkan_user_id) . '" class="btn btn-light-warning icon-btn b-r-4" ><span class="material-symbols-outlined">stacked_line_chart</span></a>';
//                    $buttons .= '<a href="javascript:void(0)" class="delete-icon btn btn-light-danger icon-btn b-r-4" data-id="'.$row->dinetkan_user_id.'"><span class="material-symbols-outlined">delete</span></a>';

                        return $buttons;
                    }
                    if($row->status == 4){
                        return '<a href="' . route('dinetkan.users_dinetkan.detail2', $row->dinetkan_user_id) . '" type="button" class="btn btn-light-primary" ><span class="material-symbols-outlined">visibility</span></a>';
                    }
                })
                ->editColumn('company', function ($row){
                    $cname = '';
                    if($row->company){
                        $cname = $row->company->name;
                    }
                   return $cname;
                })
                ->toJson();
        }
}
}

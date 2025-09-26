<?php


namespace App\Http\Controllers\Kemitraan;


use App\Http\Controllers\Controller;
use App\Http\Requests\Dinetkan\UserDinetkanRequest;
use App\Mail\TestEmail;
use App\Models\AdminDinetkanInvoice;
use App\Models\CategoryLicenseDinetkan;
use App\Models\Company;
use App\Models\CountNumbering;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\LicenseDinetkan;
use App\Models\Mapping\Pop;
use App\Models\MappingUserLicense;
use App\Models\Mikrotik\Nas;
use App\Models\Partnership\Mitra;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Pppoe\PppoeUser;
use App\Models\Province;
use App\Models\User;
use App\Models\UserDinetkan;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\ServiceStatusEnum;
use App\Enums\DinetkanInvoiceStatusEnum;

class KemitraanUsersController extends Controller
{
    public function index()
    {
        $totalservice = MappingUserLicense::where('id_mitra', multi_auth()->id_mitra)->count();
        $userdinetkan = UserDinetkan::query()->where('id_mitra_sales', multi_auth()->id_mitra)->get();
        $totaluser = $userdinetkan->count();
        if (request()->ajax()) {
            $userdinetkan = UserDinetkan::query()->where('id_mitra_sales', multi_auth()->id_mitra)->get();
            return DataTables::of($userdinetkan)
                ->addIndexColumn()
                ->editColumn('id_pelanggan', function ($row) {
                    return $row->dinetkan_user_id;
                })
                ->editColumn('full_name', function ($row) {
                    return $row->first_name." ".$row->last_name;
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i');
                })
                ->toJson();;
        }
        return view('backend.kemitraan.users.index',
            compact(
                'totaluser',
                'totalservice'
            ));
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
//        $request->validated();
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
                'id_mitra_sales' => multi_auth()->id_mitra,
                'created_by' => multi_auth()->id_mitra
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


//            $details = [
//                'email' => $request->email,
//                'subject' => 'Registrasi',
//                'username' => $request->email,
//                'password' => $password
//            ];
//            Mail::to($details['email'])->send(new TestEmail($details));
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

    public function keuangan_dinetkan()
    {
        if (multi_auth()->role !== 'Mitra') {
            $incomeMonth = TransaksiMitra::where('shortname', multi_auth()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->month)
                ->sum('nominal');
            $incomeLastMonth = TransaksiMitra::where('shortname', multi_auth()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->subMonth()->month)
                ->sum('nominal');
            $incomeYear = TransaksiMitra::where('shortname', multi_auth()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->whereYear('tanggal', Carbon::today()->year)
                ->sum('nominal');
            $totalmitra = Mitra::where('shortname', multi_auth()->shortname)->count();
        } else {
            $incomeMonth = TransaksiMitra::where('shortname', multi_auth()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->where('mitra_id', multi_auth()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->month)
                ->sum('nominal');
            $incomeLastMonth = TransaksiMitra::where('shortname', multi_auth()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->where('mitra_id', multi_auth()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->subMonth()->month)
                ->sum('nominal');
            $incomeYear = TransaksiMitra::where('shortname', multi_auth()->shortname)
                ->where('tipe', 'Pemasukan')
                ->where('is_dinetkan', 1)
                ->where('mitra_id', multi_auth()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->sum('nominal');
            $totalmitra = 0;
        }

        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $transaksi = TransaksiMitra::query()
                    ->where('shortname', multi_auth()->shortname)
                    ->where('mitra_id', multi_auth()->id)
                    ->where('is_dinetkan', 1)
                    ->whereBetween('tanggal', [Carbon::parse($start_date)->format('Y-m-d 00:00:00'), Carbon::parse($end_date)->format('Y-m-d 23:59:59')]);
            } else {
                $transaksi = TransaksiMitra::query()
                    ->where('shortname', multi_auth()->shortname)
                    ->where('mitra_id', multi_auth()->id)
                    ->with('mitra')
                    ->where('is_dinetkan', 1);
            }
            return DataTables::of($transaksi)
                ->addColumn('nama_mitra', function ($row) {
                    return $row->mitra->name;
                })
                ->addColumn('action', function ($row) {
                    if (in_array(multi_auth()->role, ['Admin', 'Kasir'])) {
                        return '
                        <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '" 
                            class="btn btn-danger text-white" 
                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <i class="ti ti-trash fs-5 align-middle"></i>
                        </a>';
                    }
                })
                ->toJson();
        }

        return view('backend.kemitraan.keuangan.index', compact('incomeMonth', 'incomeLastMonth', 'incomeYear', 'totalmitra'));
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
            ->where('id_mitra_sales', multi_auth()->id_mitra)
            ->get();
        return view('backend.kemitraan.invoice.index',
            compact(
                'licenses',
                'categories','resellers'
            ));
    }


    public function unpaid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
            ->where('due_date','>=', \Illuminate\Support\Carbon::now())
            ->whereHas('mapping_mitra')
            ->with('mapping_mitra')
            ->get();

        return DataTables::of($invoices)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                DinetkanInvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    DinetkanInvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
//                return '<a href="' . route('dinetkan.invoice_dinetkan.detail', $row->no_invoice) . '"  class="btn btn-light-primary " title="Manual Pay">Manual Pay</a>';
                return '<a target="_blank" href="' . route('admin.invoice_dinetkan', $row->no_invoice) . '" class="btn btn-xs btn-light-info" title="Pay">Pay</a>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }



    public function paid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::query()
            ->where('status', DinetkanInvoiceStatusEnum::PAID)
            ->whereHas('mapping_mitra')
            ->with('mapping_mitra')
            ->get();


        return DataTables::of($invoices)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name ? $admin->first_name : $admin->shortname;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->admin;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                DinetkanInvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    DinetkanInvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('dinetkan.invoice_dinetkan.detail', $row->no_invoice) . '"  class="btn btn-light-primary " title="Manual Pay">Manual Pay</a>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }


    public function expired(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::query()
            ->whereIn('status', [DinetkanInvoiceStatusEnum::EXPIRED,DinetkanInvoiceStatusEnum::CANCEL])
            ->whereHas('mapping_mitra')
            ->with('mapping_mitra')
            ->get();
        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                DinetkanInvoiceStatusEnum::EXPIRED => '<span class="badge bg-">warning</span>',
                    DinetkanInvoiceStatusEnum::CANCEL => '<span class="badge bg-danger">CANCEL</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }
}

<?php

namespace App\Http\Controllers\Admin\Account;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\ServiceStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AdminDinetkanInvoice;
use App\Models\CategoryLicenseDinetkan;
use App\Models\Districts;
use App\Models\DocType;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\MasterMetro;
use App\Models\MasterMikrotik;
use App\Models\MasterPop;
use App\Models\Province;
use App\Models\Regencies;
use App\Models\ServiceDetail;
use App\Models\UserDinetkan;
use App\Models\UserDinetkanGraph;
use App\Models\UserDoc;
use App\Models\UserWhatsappGroup;
use App\Models\Villages;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class InvoiceDinetkanController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.accounts.invoices_dinetkan.index');
    }

    public function order(Request $request)
    {
        return view('backend.accounts.invoices_dinetkan.order');
    }

    public function unpaid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
//            ->where('due_date','>=', Carbon::now())
            ->get();
//        print_r($invoices);exit;
        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    DinetkanInvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    DinetkanInvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a target="_blank" href="' . route('admin.invoice_dinetkan', $row->no_invoice) . '" class="btn btn-xs btn-light-info" title="Pay">Pay</a>';
            })
            ->editColumn('total_all', function ($row) {
                return ($row->price + $row->total_ppn + $row->price_adon  + $row->price_adon_monthly);
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
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::PAID)
            ->get();

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    DinetkanInvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    DinetkanInvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.invoice_dinetkan', $row->no_invoice) . '" class="btn btn-xs btn-info" title="Pay">Detail</a>';
            })
            ->editColumn('total_all', function ($row) {
                return ($row->price + $row->total_ppn + $row->price_adon  + $row->price_adon_monthly);
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
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->whereIn('status', [DinetkanInvoiceStatusEnum::EXPIRED,DinetkanInvoiceStatusEnum::CANCEL])
            ->where('due_date','<', Carbon::now())
            ->get();
        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('total_all', function ($row) {
                return ($row->price + $row->total_ppn + $row->price_adon  + $row->price_adon_monthly);
            })
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
    public function active(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }
        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::ACTIVE)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                    ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                    ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                    default => '<span class="badge bg-danger">Canceled</span>',
                        };
                    })
            ->addColumn('action', function ($row) {
                return '<a href="" class="btn btn-xs btn-danger" title="Cancel">Cancel</a>';
            })
            ->editColumn('service_id', function ($row) {
                return '<a href="' . route('admin.account.invoice_dinetkan.order.detail_service', ($row->service_id ? $row->service_id : 0)) . '" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->rawColumns(['action', 'status','service_id'])
            ->toJson();
    }


    public function inactive(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::INACTIVE)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();

        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                    ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                    ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                    default => '<span class="badge bg-danger">Canceled</span>',
                        };
                    })
            ->addColumn('action', function ($row) {
                return '<a href="" class="btn btn-xs btn-danger" title="Cancel">Cancel</a>';
            })
            ->editColumn('service_id', function ($row) {
                return '<a href="' . route('admin.account.invoice_dinetkan.order.detail_service', ($row->service_id ? $row->service_id : 0)) . '" class="btn btn-light-success" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }


    public function suspend(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::SUSPEND)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();


        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                        ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                        ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                        ServiceStatusEnum::SUSPEND => '<span class="badge bg-danger">SUSPEND</span>',
                                        default => '<span class="badge bg-danger">Canceled</span>',
                            };
                        })
            ->addColumn('action', function ($row) {
                return '<a href="" class="btn btn-xs btn-danger" title="Cancel">Cancel</a>';
            })
            ->editColumn('service_id', function ($row) {
                return '<a href="' . route('admin.account.invoice_dinetkan.order.detail_service', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-light-success btn-sm" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->rawColumns(['action', 'status', 'service_id'])
            ->toJson();
    }


    public function overdue(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }


        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::OVERDUE)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();


        return DataTables::of($mapping)
            ->addIndexColumn()

            ->editColumn('first_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->first_name;
            })
            ->editColumn('last_name', function ($row) {
                $admin = $row->user;
                if (!$admin) {
                    return '';
                }
                return $admin->last_name;
            })
            ->editColumn('service', function ($row) {
                $admin = $row->service;
                if (!$admin) {
                    return '';
                }
                return $admin->name;
            })
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                ServiceStatusEnum::NEW => '<span class="badge bg-warning">NEW</span>',
                                        ServiceStatusEnum::INACTIVE => '<span class="badge bg-warning">INACTIVE</span>',
                                        ServiceStatusEnum::ACTIVE => '<span class="badge bg-success">ACTIVE</span>',
                                        ServiceStatusEnum::OVERDUE => '<span class="badge bg-danger">OVERDUE</span>',
                                        default => '<span class="badge bg-danger">Canceled</span>',
                            };
                        })
            ->addColumn('action', function ($row) {
                return '<a href="" class="btn btn-xs btn-danger" title="Cancel">Cancel</a>';
            })
            ->editColumn('service_id', function ($row) {
//                return '<a href="' . route('admin.account.invoice_dinetkan.order.detail_service', ($row->service_id ? $row->service_id : 0) ) . '" type="button" class="btn btn-primary btn-xs" >'.($row->service_id ? $row->service_id : 0).'</a>';
                return '<a href="' . route('dinetkan.users_dinetkan.detail', $row->dinetkan_user_id) . '" class="btn btn-light-success icon-btn b-r-4" >'.($row->service_id ? $row->service_id : 0).'</a>';
            })
            ->editColumn('due_date', function ($row) {
                return $row->due_date;
            })
            ->rawColumns(['action', 'status','service_id'])
            ->toJson();
    }


    function detail_service($service_id){

        $vlan = get_vlan_mikrotik();
        cacti_logout();
        cacti_login();
        $mapping = MappingUserLicense::where('service_id', $service_id)->with('service')->first();
        $service_detail = ServiceDetail::where('service_id', $service_id)->first();
        $provinces = [];
        $regencies = [];
        $districts = [];
        $villages = [];
        if ($service_detail->province_id) {
            $regencies = Regencies::where('province_id', $service_detail->province_id)->get();
        }
        $districts = [];
        if ($service_detail->regency_id) {
            $districts = Districts::where('regency_id', $service_detail->regency_id)->get();
        }
        $villages = [];
        if ($service_detail->district_id) {
            $villages = Villages::where('district_id', $service_detail->district_id)->get();
        }
        $docType = DocType::all();
        $listDoc = UserDoc::with('docType')->where('service_id', $service_id)->get();

        $provinces = Province::query()
            ->orderBy('name', 'asc')
            ->get();
        $userdinetkanGraph = null;
        if($mapping){
            $userdinetkanGraph = UserDinetkanGraph::where('dinetkan_user_id',$mapping->dinetkan_user_id)->where('service_id', $service_id)->get();
        }
        $pop = MasterPop::get();
        $tree = get_tree_mrtg();
        $metro = MasterMetro::all();
        $mikrotik = MasterMikrotik::all();
        $mikrotik_detail = null;
        if($service_detail){
            if($service_detail->id_mikrotik != null){
                $mikrotik_detail = MasterMikrotik::where('id', $service_detail->id_mikrotik)->first();
            }
        }
        $edited = false;
        if(!$service_detail->province_id || !$service_detail->regency_id || !$service_detail->district_id || !$service_detail->village_id
            || !$service_detail->address || !$service_detail->latitude || !$service_detail->longitude){
            $edited = true;
        }
        $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
        return view('backend.accounts.invoices_dinetkan.service_detail',
            compact('docType', 'mapping', 'service_detail', 'listDoc'
                ,'provinces','regencies','districts','villages','userdinetkanGraph','pop','tree','metro','vlan','mikrotik','mikrotik_detail','wag','edited'));
    }

    public function update_service_detail(Request $request)
    {
        try {
            $service = ServiceDetail::where('service_id', $request->service_id)->first();
            $data = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'address' => $request->address,
            ];
            if ($service) {
                $service->update($data);
            }
            if (!$service) {
                ServiceDetail::create($data);
            }
            return response()->json(['message' => 'Service Update successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error Service Update: ' . $e->getMessage()], 500);
        }
    }
}

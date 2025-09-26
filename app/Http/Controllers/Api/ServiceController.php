<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\DocType;
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
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\ServiceStatusEnum;

class ServiceController extends Controller
{
    public function active(Request $request)
    {
        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::ACTIVE)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();
        return response()->json($mapping);
    }

    public function inactive(Request $request)
    {
        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::INACTIVE)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();

        return response()->json($mapping);
    }

    public function suspend(Request $request)
    {
        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::SUSPEND)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();

        return response()->json($mapping);
    }


    public function overdue(Request $request)
    {
        $user = UserDinetkan::where('dinetkan_user_id',$request->user()->dinetkan_user_id)->first();
        $mapping =  MappingUserLicense::where('status', ServiceStatusEnum::OVERDUE)->where('dinetkan_user_id', $user->dinetkan_user_id)->with('user')->with('service')->get();

        return response()->json($mapping);
    }


    function detail_service($id){
        $service_id = $id;
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
            return response()->json(
                array(
                    'docType' => $docType,
                    'listDoc' => $listDoc,
                    'mapping' => $mapping,
                    'service_detail' => $service_detail,
                    'provinces' => $provinces,
                    'regencies' => $regencies,
                    'districts' => $districts,
                    'villages' => $villages,
                    'userdinetkanGraph' => $userdinetkanGraph,
                    'mikrotik' => $mikrotik,
                    'mikrotik_detail' => $mikrotik_detail,
                    'edited' => $edited));
            ;
    }
}

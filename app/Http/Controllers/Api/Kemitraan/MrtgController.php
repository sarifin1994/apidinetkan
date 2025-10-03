<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\Http\Controllers\Controller;
use App\Models\MappingUserLicense;
use App\Models\UserDinetkanGraph;
use Illuminate\Http\Request;
use App\Enums\ServiceStatusEnum;

class MrtgController extends Controller
{
    public function list_graph(Request $request){
        $services = MappingUserLicense::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', ServiceStatusEnum::ACTIVE->value)
            ->with('service')
            ->with('service_detail')
            ->with('service_libre')->get();
        $graph = UserDinetkanGraph::query()->where('dinetkan_user_id', $request->user()->dinetkan_user_id)->get();
        return response()->json($services);
    }

}

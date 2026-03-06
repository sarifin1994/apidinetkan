<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\Http\Controllers\Controller;
use App\Models\PingGroup;
use Illuminate\Http\Request;
class PingMonitorController extends Controller
{
    public function list_group(Request $request){
        $target = PingGroup::query()->with('targets')->get();
        return response()->json($target);
    }
}

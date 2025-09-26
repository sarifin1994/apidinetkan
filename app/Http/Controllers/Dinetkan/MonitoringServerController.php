<?php


namespace App\Http\Controllers\Dinetkan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CouponRequest;
use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\LogMonitoringServer;
use App\Models\MasterGroupServer;
use App\Models\MasterPop;
use App\Models\MasterServer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\License;


class MonitoringServerController extends Controller
{
    public function index(Request $request)
    {
    }

    public function group_server(Request $request){
        $masterGroupServer = MasterGroupServer::all();
        return view('dinetkan.monitoring_server.group', compact(
            'masterGroupServer'));
    }

    public function group_server_store(Request $request){
        try{
            MasterGroupServer::create([
                'name' => $request->name
            ]);

            return redirect()->back()->with('success', 'Group Create successfully');
        }catch (\Exception $e){
            return redirect()->back()->with('error', 'Group Create un-successfully');

        }
    }

    public function group_server_single($id){
        $data = MasterGroupServer::where('id', $id)->first();
        return response()->json($data);
    }

    public function group_server_update(Request $request,$id){
        try {
            $masterGroupServer = MasterGroupServer::where('id', $id)->first();
            $data = [
                'name' => $request->name
            ];
            $masterGroupServer->update($data);
            return redirect()->back()->with('success', 'Group update successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error update Group');
        }
    }

    public function group_server_delete($id){
        try {
            $data = MasterGroupServer::where('id', $id)->first();
            $data->delete();
            return redirect()->back()->with('success', 'Group deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting Group');
        }
    }


//    server


    public function server(Request $request){
        $server = MasterServer::with('group')->get();
        $masterGroupServer = MasterGroupServer::all();
        return view('dinetkan.monitoring_server.server', compact(
            'server','masterGroupServer'));
    }

    public function server_store(Request $request){
        try{
            MasterServer::create([
                'name' => $request->name,
                'address' => $request->address,
                'group_id' => $request->group_id,
                'is_notif' => $request->is_notif,
            ]);

            return redirect()->back()->with('success', 'Group Create successfully');
        }catch (\Exception $e){
            return redirect()->back()->with('error', 'Group Create un-successfully');

        }
    }

    public function server_single($id){
        $data = MasterServer::where('id', $id)->first();
        return response()->json($data);
    }

    public function server_update(Request $request,$id){
        try {
            $masterServer = MasterServer::where('id', $id)->first();
            $data = [
                'name' => $request->name,
                'address' => $request->address,
                'group_id' => $request->group_id,
                'is_notif' => $request->is_notif,
            ];
            $masterServer->update($data);
            return redirect()->back()->with('success', 'Group update successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error update Group');
        }
    }

    public function server_delete($id){
        try {
            $data = MasterServer::where('id', $id)->first();
            $data->delete();
            return redirect()->back()->with('success', 'Group deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting Group');
        }
    }

    public function log(Request $request){
        $filter = $request->filter;
        $startdate = Carbon::now()->format('Y-m-d');
        $type = "Today";

        if($filter == "today"){
            $startdate = Carbon::now()->format('Y-m-d');
            $type = "Today";
        }
        if($filter == "week"){
            $startdate = Carbon::now()->subWeeks(1)->format('Y-m-d');
            $type = "Last 1 Week";
            
        }
        if($filter == "month"){
            $startdate = Carbon::now()->subMonths(1)->format('Y-m-d');
            $type = "Last 1 Month";
        }
        if($filter == "3month"){
            $startdate = Carbon::now()->subMonths(3)->format('Y-m-d');
            $type = "Last 3 Month";
        }
        if($filter == "6month"){
            $startdate = Carbon::now()->subMonths(6)->format('Y-m-d');
            $type = "Last 6 Month";
        }
        if($filter == "1year"){
            $startdate = Carbon::now()->subYears(1)->format('Y-m-d');
            $type = "Last 1 Year";
        }

        $servers = MasterServer::get();
        $results=[];
        foreach ($servers as $sv){
            $todayAll = LogMonitoringServer::where('created_at','>', $startdate.' 00:00:00')
                ->where('created_at','<', Carbon::now()->format('Y-m-d').' 23:59:59')
                ->where('id_server', $sv->id)->get();
            $todayAllCount = count($todayAll);

            $todayAllUp = LogMonitoringServer::where('created_at','>', $startdate.' 00:00:00')
                ->where('created_at','<', Carbon::now()->format('Y-m-d').' 23:59:59')
                ->where('id_server', $sv->id)
                ->where('status', 'UP')->get();
            $todayAllCountUp = count($todayAllUp);

            $todayAllDown = LogMonitoringServer::where('created_at','>', $startdate.' 00:00:00')
                ->where('created_at','<', Carbon::now()->format('Y-m-d').' 23:59:59')
                ->where('id_server', $sv->id)
                ->where('status', 'DOWN')->get();
            $todayAllCountDown = count($todayAllDown);
            $results[] = array(
                'server_id' => $sv->id,
                'server_name' => $sv->name,
                'server_address' => $sv->address,
                // 'todayall' => $todayAll,
                'todayAllCount' => $todayAllCount,

                // 'todayAllUp' => $todayAllUp,
                'todayAllCountUp' => $todayAllCountUp,
                'percentUp' => ($todayAllCount == 0 ? 0 :round(($todayAllCountUp / $todayAllCount * 100), 4)),

                // 'todayalldown' => $todayalldown,
                'todayAllCountDown' => $todayAllCountDown,
                'percentDown' => ($todayAllCount == 0 ? 0 : round(($todayAllCountDown / $todayAllCount * 100), 4)),
            );
        }
//        print_r($results);exit;
        return view('dinetkan.monitoring_server.log', compact('results','type'));
    }

}

<?php


namespace App\Http\Controllers\Dinetkan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CouponRequest;
use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\MasterMikrotik;
use App\Models\MasterPop;
use App\Models\ServiceDetail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\License;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;


class MasterMikrotikController extends Controller
{
    public function index(Request $request)
    {
        $mikrotik = MasterMikrotik::all();
        return view('backend.dinetkan.master_mikrotik', compact(
            'mikrotik'));
    }

    public function store(Request $request){
        try{
            MasterMikrotik::create([
                'name' => $request->name,
                'ip' => $request->ip,
                'port' => $request->port,
                'username' => $request->username,
                'password' => $request->password,
//                'time_out' => $request->time_out,
            ]);

            return redirect()->back()->with('success', 'Mikrotik Create successfully');
        }catch (\Exception $e){
            return redirect()->back()->with('error', 'Mikrotik Create un-successfully');

        }

    }

    public function single($id){
        $data = MasterMikrotik::where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id){
        try {
            $data = MasterMikrotik::where('id', $id)->first();
            $data->delete();
            return redirect()->back()->with('success', 'Mikrotik deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting Mikrotik');
        }
    }

    public function update(Request $request,$id){
        try {
            $masterPop = MasterMikrotik::where('id', $id)->first();
            $data = [
                'name' => $request->name,
                'ip' => $request->ip,
                'port' => $request->port,
                'username' => $request->username,
                'password' => $request->password,
                'time_out' => $request->time_out,
            ];
            $masterPop->update($data);
            return redirect()->back()->with('success', 'Mikrotik update successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error update Mikrotik');
        }
    }

    public function get_vlan($id){
        try{
            $mikrotik = MasterMikrotik::where('id', $id)->first();
            $client = new Client([
                'host' => $mikrotik->ip,
                'user' => $mikrotik->username,
                'pass' => $mikrotik->password,
                'port' => $mikrotik->port, // port API Mikrotik kamu
                'timeout' => 10,
            ]);
            $query = new Query('/interface/vlan/print');
            $hasil = $client->query($query)->read();
            $response = [];
            $response[] = [
                'id' => '-',
                'name' => 'Pilih VLAN'
            ];
            foreach ($hasil as $row){
                $response[] = [
                    'id' => $row['.id']."|".$row['name'],
                    'name' => $row['name']
                ];
            }
            return response()->json($response);
        }catch (\Exception $ex){
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }

    public function get_vlan_single($service_id){
        $service = ServiceDetail::where('service_id', $service_id)->first();
        $mikrotik = MasterMikrotik::where('id', $service->id_mikrotik)->first();
        if($mikrotik){
            $client = new Client([
                'host' => $mikrotik->ip,
                'user' => $mikrotik->username,
                'pass' => $mikrotik->password,
                'port' => $mikrotik->port, // port API Mikrotik kamu
                'timeout' => 10,
            ]);
            $query = new Query('/interface/vlan/print');
            $hasil = $client->query($query)->read();
            $response = null;
            foreach ($hasil as $row){
                if($row['.id'] == $service->vlan_id){
                    $response = $row;
                }
            }
            return response()->json($response);
        }
        return response()->json([]);
    }

    public function disabled_vlan($service_id){
        $service = ServiceDetail::where('service_id', $service_id)->first();
        $mikrotik = MasterMikrotik::where('id', $service->id_mikrotik)->first();
        $client = new Client([
            'host' => $mikrotik->ip,
            'user' => $mikrotik->username,
            'pass' => $mikrotik->password,
            'port' => $mikrotik->port, // port API Mikrotik kamu
            'timeout' => 10,
        ]);
        $query = new Query('/interface/vlan/set');
        $query->equal('.id', $service->vlan_id);  // Ganti *F dengan ID VLAN yang ingin diubah
        $query->equal('disabled', 'yes');  // 'no' untuk enable
        $hasil = $client->query($query)->read();
        return response()->json($hasil);
    }

    public function enabled_vlan($service_id){
        $service = ServiceDetail::where('service_id', $service_id)->first();
        $mikrotik = MasterMikrotik::where('id', $service->id_mikrotik)->first();
        $client = new Client([
            'host' => $mikrotik->ip,
            'user' => $mikrotik->username,
            'pass' => $mikrotik->password,
            'port' => $mikrotik->port, // port API Mikrotik kamu
            'timeout' => 10,
        ]);
        $query = new Query('/interface/vlan/set');
        $query->equal('.id', $service->vlan_id);  // Ganti *F dengan ID VLAN yang ingin diubah
        $query->equal('disabled', 'no');  // 'no' untuk enable
        $hasil = $client->query($query)->read();
        return response()->json($hasil);
    }

}

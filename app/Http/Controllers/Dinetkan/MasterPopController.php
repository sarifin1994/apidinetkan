<?php


namespace App\Http\Controllers\Dinetkan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CouponRequest;
use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\MasterPop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\License;


class MasterPopController extends Controller
{
    public function index(Request $request)
    {
        $pop = MasterPop::all();
        return view('backend.dinetkan.master_pop', compact(
            'pop'));
    }

    public function store(Request $request){
        try{
            MasterPop::create([
                'name' => $request->name,
                'pic_name' => $request->pic_name,
                'pic_whatsapp' => $request->pic_whatsapp,
                'ip' => $request->ip,
            ]);

            return redirect()->back()->with('success', 'POP Create successfully');
        }catch (\Exception $e){
            return redirect()->back()->with('error', 'POP Create un-successfully');

        }

    }

    public function get_coupon_single($id){
        $data = Coupon::with('user','license')->find($id);

        return response()->json($data);
    }

    public function single($id){
        $data = MasterPop::where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id){
        try {
            $data = MasterPop::where('id', $id)->first();
            $data->delete();
            return redirect()->back()->with('success', 'POP deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting POP');
        }
    }

    public function update(Request $request,$id){
        try {
            $masterPop = MasterPop::where('id', $id)->first();
            $data = [
                'name' => $request->name,
                'pic_name' => $request->pic_name,
                'pic_whatsapp' => $request->pic_whatsapp,
                'ip' => $request->ip,
            ];
            $masterPop->update($data);
            return redirect()->back()->with('success', 'POP update successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error update POP');
        }
    }

}

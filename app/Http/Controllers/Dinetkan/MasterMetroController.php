<?php


namespace App\Http\Controllers\Dinetkan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CouponRequest;
use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\MasterMetro;
use App\Models\MasterPop;
use App\Models\Province;
use App\Models\User;
use App\Models\UserWhatsappGroup;
use Illuminate\Http\Request;
use App\Models\License;


class MasterMetroController extends Controller
{
    public function index(Request $request)
    {
        $provinces = [];
        $regencies = [];
        $districts = [];
        $villages = [];

        $provinces = Province::query()
            ->orderBy('name', 'asc')
            ->get();
        $metro = MasterMetro::with('province')->with('regency')->with('district')->with('village')->get();
        $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
        return view('backend.dinetkan.master_metro', compact(
            'metro','provinces',
            'regencies',
            'districts',
            'villages',
            'wag'));
    }

    public function store(Request $request){
        try{
            $wag = UserWhatsappGroup::where('group_id', $request->id_wag)->first();
            MasterMetro::create([
                'name' => $request->name,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'address' => $request->address,
                'pic' => $request->pic,
                'id_wag' => $request->id_wag,
                'name_wag' => $wag ? $wag->group_name : '',
                'pic_phone' => $request->pic_phone,
            ]);

            return response()->json(['message' => 'Data created successfully'], 201);
        }catch (\Exception $e){
            return response()->json(['message' => 'Error creating data: ' . $e->getMessage()], 500);

        }

    }

    public function update(Request $request){
        try{
            $wag = UserWhatsappGroup::where('group_id', $request->id_wag)->first();
            $metro = MasterMetro::where('id', $request->id)->first();
            $data = [
                'name' => $request->name,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'address' => $request->address,
                'pic' => $request->pic,
                'id_wag' => $request->id_wag,
                'name_wag' => $wag->group_name,
                'pic_phone' => $request->pic_phone,
            ];

            $metro->update($data);
            return response()->json(['message' => 'Data updated successfully'], 201);
        }catch (\Exception $e){
            return response()->json(['message' => 'Error updated data: ' . $e->getMessage()], 500);

        }

    }

    public function single($id){
        $data = MasterMetro::where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id){
        try {
            $data = MasterMetro::where('id', $id)->first();
            $data->delete();
            return redirect()->back()->with('success', 'POP deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting POP');
        }
    }

}

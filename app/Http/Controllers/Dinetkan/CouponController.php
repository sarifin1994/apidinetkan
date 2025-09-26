<?php


namespace App\Http\Controllers\Dinetkan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CouponRequest;
use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\License;


class CouponController extends Controller
{
    public function index(Request $request)
    {
        $resellers = User::where('id_group','>',0)
            ->select('id','name','shortname')
            ->get();
        $licenses = License::all();
        $coupon = Coupon::all();
        return view('dinetkan.coupon', compact(
            'resellers',
            'licenses',
            'coupon'));
    }

    public function create_coupon(CouponRequest $request){
        $request->validated();
        try{
            $resellers = User::where('id_group','>',0)
                ->select('id','name','shortname')
                ->get();
            $licenses = License::all();
            $coupon = Coupon::create([
                'coupon_name' => $request->coupon_name,
                'used' => $request->used,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'percent' => $request->percent,
                'nominal' => $request->nominal,

            ]);
            if(isset($request->license_id)){
                foreach ($request->license_id as $lic){
                    Coupon_license::create([
                        'coupon_id' => $coupon->id,
                        'license_id' => $lic
                    ]);
                }
            }
            if(!isset($request->license_id)){
                foreach ($licenses as $lic){
                    Coupon_license::create([
                        'coupon_id' => $coupon->id,
                        'license_id' => $lic->id
                    ]);
                }
            }
            if(isset($request->user_id)){
                foreach ($request->user_id as $lic){
                    Coupon_user::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $lic
                    ]);
                }
            }
            if(!isset($request->user_id)){
                foreach ($resellers as $lic){
                    Coupon_user::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $lic->id
                    ]);
                }
            }
            return response()->json(['message' => 'Coupon created successfully'], 201);
        }catch (\Exception $e){
            return response()->json(['message' => 'Error creating coupon: ' . $e->getMessage()], 500);

        }

    }

    public function get_coupon_single($id){
        $data = Coupon::with('user','license')->find($id);

        return response()->json($data);
    }

}

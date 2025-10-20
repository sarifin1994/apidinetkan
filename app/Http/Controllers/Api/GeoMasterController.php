<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\Province;
use App\Models\Regencies;
use App\Models\Villages;

class GeoMasterController extends Controller
{

    public function province()
    {
        $provinces = Province::query()
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($provinces);
    }

    public function regencies($province_id=0)
    {
        $regencies = Regencies::query()
            ->orderBy('name', 'asc')
            ->get();
        if($province_id > 0){
            $regencies = Regencies::query()
                ->where('province_id', $province_id)
                ->orderBy('name', 'asc')
                ->get();
        }
        return response()->json($regencies);
    }

    public function districts($regency_id=0)
    {
        $districts = Districts::query()
            ->orderBy('name', 'asc')
            ->get();
        if($regency_id>0){
            $districts = Districts::query()
                ->where('regency_id', $regency_id)
                ->orderBy('name', 'asc')
                ->get();

        }
        return response()->json($districts);
    }

    public function villages($district_id=0)
    {
        $villages = Villages::query()
            ->orderBy('name', 'asc')
            ->get();
        if($district_id>0){
            $villages = Villages::query()
                ->where('district_id', $district_id)
                ->orderBy('name', 'asc')
                ->get();
        }
        return response()->json($villages);
    }

    // single

    public function province_single($province_id)
    {
        $provinces = Province::query()
            ->where('id', $province_id)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($provinces);
    }

    public function regencies_single($province_id)
    {
        $regencies = Regencies::query()
            ->where('id', $province_id)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($regencies);
    }

    public function districts_single($regency_id)
    {
        $districts = Districts::query()
            ->where('id', $regency_id)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($districts);
    }

    public function villages_single($district_id)
    {
        $villages = Villages::query()
            ->where('id', $district_id)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($villages);
    }
}

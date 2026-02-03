<?php


namespace App\Http\Controllers\Api\Kemitraan;

use App\Http\Controllers\Controller;
use App\Models\UserDinetkan;
use App\Models\UserDoc;
use Illuminate\Http\Request;

class AccountInfoController extends Controller
{
    public function info(Request $request){
        $user = $request->user();
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $user->dinetkan_user_id)
            ->with('province')
            ->with('regency')
            ->with('district')
            ->with('village')
            ->first();
        return response()->json($userdinetkan);
    }

    public function list_document(Request $request){
        $user = $request->user();
        $listDoc = UserDoc::with('docType')->where('user_id', $user->id)->get();
        return response()->json($listDoc);
    }

    public function update_info(Request $request){
        $user = $request->user();
        $userdinetkan = UserDinetkan::where('dinetkan_user_id', $user->dinetkan_user_id);
        $checkwa = UserDinetkan::query()->where('whatsapp', $request->whatsapp)->where('dinetkan_user_id', '!=', $user->dinetkan_user_id)->get();
        if(count($checkwa) > 0 ){
            return response()->json(['message' => 'nomor whatsapp sudah terdaftar'], 201);
        }
        if($userdinetkan){
            $data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'whatsapp' => $request->whatsapp,
                'id_card' => $request->id_card,
                'npwp' => $request->npwp,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'address' => $request->address
            );
            $userdinetkan->update($data);
            return response()->json(['message' => 'data berhasil di update'], 201);
        }else{
            return response()->json(['message' => 'data gagal di update'], 500);
        }

    }

}

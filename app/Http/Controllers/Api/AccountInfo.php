<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\UserDinetkan;
use App\Models\UserDoc;
use Illuminate\Http\Request;

class AccountInfo extends Controller
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

}

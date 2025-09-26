<?php

namespace App\Http\Controllers\Admin\Account;

use App\Models\Districts;
use App\Models\DocType;
use App\Models\Province;
use App\Models\Regencies;
use App\Models\UserDinetkan;
use App\Models\UserDoc;
use App\Models\Villages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AccountInfoDinetkanController extends Controller
{
    public function index(Request $request)
    {
        $docType = DocType::all();
        $listDoc = UserDoc::with('docType')->where('user_id', multi_auth()->id)->get();
        $userdinetkan = UserDinetkan::where('id', multi_auth()->id)
            ->with('province')
            ->with('regency')
            ->with('district')
            ->with('village')
            ->first();
//        echo json_encode($userdinetkan);exit;
        return view('backend.accounts.info_dinetkan.index', compact(
            'userdinetkan','docType','listDoc'
        ));
    }

    public function mrtg(){
        $userdinetkan = UserDinetkan::where('id', multi_auth()->id)->first();
        $this->cacti_login();
        $this->get_tree_node_mrtg($userdinetkan->graph);
        return view('backend.accounts.info_dinetkan.mrtg', compact(
            'userdinetkan'
        ));
    }

    protected function cacti_login(){
        $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
        $apiUrl = env('CACTI_ENDPOINT').'cacti/login/'.$_id;
        try {
            $params = array(
                "action" =>"login",
                "login_username" => "wijaya",
                "login_password" => "wijaya@2024"
            );
            // Kirim POST request ke API eksternal
            $response = Http::post($apiUrl, $params);
            // Periksa apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }


    public function get_tree_node_mrtg($graph){
        // step 2
        try {
            $params = array(
                "rra_id" => 0,
                "local_graph_id" => $graph,
                "graph_start" => Carbon::createFromFormat('Y-m-d', Carbon::now())->addMonthsWithNoOverflow(0)->toDateString(),
                "graph_end" => Carbon::createFromFormat('Y-m-d', Carbon::now())->addMonthsWithNoOverflow(1)->toDateString(),
                "graph_height" => 200,
                "graph_width" => 700
            );
            $_id = Str::lower(Str::replace(' ', '', multi_auth()->name));
            $apiUrl = env('CACTI_ENDPOINT').'cacti/graph_json/'.$_id.'?' . urldecode(http_build_query($params)) ;
            // Kirim POST request ke API eksternal
            $response = Http::get($apiUrl);

            // Periksa apakah request berhasil
            print_r($response->json());exit;
            if ($response->successful()) {
                $data = $response->json();
                return $data ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function get_info_dinetkan(){
        $userdinetkan = UserDinetkan::where('id', multi_auth()->id)
            ->with('province')
            ->with('regency')
            ->with('district')
            ->with('village')
            ->first();
        $provinces = Province::query()
            ->orderBy('name', 'asc')
            ->get();
        $regencies = [];
        if($userdinetkan->province_id){
            $regencies = Regencies::where('province_id', $userdinetkan->province_id)->get();
        }
        $districts = [];
        if($userdinetkan->regency_id){
            $districts = Districts::where('regency_id', $userdinetkan->regency_id)->get();
        }
        $villages = [];
        if($userdinetkan->district_id){
            $villages = Villages::where('district_id', $userdinetkan->district_id)->get();
        }
        $docType = DocType::all();
        $listDoc = UserDoc::with('docType')->where('user_id', multi_auth()->id)->get();
        $status_id = $userdinetkan->status;
        return view('backend.accounts.info_dinetkan.update_info', compact(
            'userdinetkan',
            'docType',
            'listDoc',
            'provinces',
            'regencies',
            'districts',
            'villages',
            'status_id'
        ));
    }

    public function update_doc_info_dinetkan(Request $request){
        $request->validate([
            'doc' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'doc_id' => 'required'
        ]);

        $file = $request->file('doc');
        // Tentukan folder penyimpanan berdasarkan jenis file
        $folder = 'user_document'; //$file->getClientOriginalExtension() == 'pdf' ? 'documents' : 'images';

        // Buat nama file unik
        $customName = multi_auth()->id  . '_' . $request->doc_id. '.' . $file->getClientOriginalExtension();

        // Simpan file ke storage/app/public/images atau storage/app/public/documents
        $path = $file->storeAs($folder, $customName, 'local');

        // Simpan ke database
        $fileUpload = new UserDoc();
        $fileUpload->file_name = $customName;
        $fileUpload->doc_id = $request->doc_id;
        $fileUpload->user_id = multi_auth()->id;
        $fileUpload->file_ext = $file->getClientOriginalExtension();
        $fileUpload->path = $path;
        $fileUpload->save();

        return redirect()->back()->with('success', 'Document updated successfully');
    }

    public function show_file($id)
    {
        if (!multi_auth()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses file ini.');
        }

        $userDoc = UserDoc::where('id', $id)->first();
//        echo $userDoc->path;exit;
        $path = storage_path('app/private/' . $userDoc->path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($path);
    }

    public function update_info_dinetkan(Request $request){
        $userdinetkan = UserDinetkan::where('id', multi_auth()->id)->first();
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

<?php

namespace App\Http\Controllers\Dinetkan;

use App\Http\Controllers\Controller;
use App\Models\UsersWhatsapp;
use App\Models\UserWhatsappGroup;
use App\Models\WatemplateDinetkan;
use App\Models\Whatsapp\Watemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsappController extends Controller
{
    public function index()
    {
        $_id = multi_auth()->id;
        $wa = UsersWhatsapp::where('user_id', $_id)->first();
        $wag = UserWhatsappGroup::where('user_id', multi_auth()->id)->get();
        if(!$wa){
            $data = array(
                'user_id' => $_id,
            );
            UsersWhatsapp::create($data);
        }
        $wa = UsersWhatsapp::where('user_id', $_id)->first();
        return view('backend.dinetkan.whatsapp.index',compact('wa','wag'));
    }

    public function update_group(Request $request){
        try{
            $userwhatsapp = UsersWhatsapp::where('user_id', multi_auth()->id)->first();
            $wag = UserWhatsappGroup::where('group_id', $request->id_wag)->first();
            $data = array(
                'group_id' => $request->id_wag,
                'group_name' => $wag->group_name
            );
            $userwhatsapp->update($data);
            return redirect()->back()->with('success', 'Group added');
        }catch (\Exception $e){
            return response()->json(['message' => 'Error creating data: ' . $e->getMessage()], 500);

        }
    }

    public function start(){
        $this->hentikan_whatsapp();
        $this->hapus_whatsapp();
        $this->tambah_whatsapp();
        $start_whatsapp = $this->start_whatsapp();
        return response()->json(['message' => 'WhatsApp Start Successfully'], 201);
    }

    public function restart(){
        $_id = multi_auth()->id;
        $userwa = UsersWhatsapp::where('user_id',$_id)->first();
        $userwa->delete();
        $this->hentikan_whatsapp();
        $this->hapus_whatsapp();
        return response()->json(['message' => 'WhatsApp Restart Successfully'], 201);
    }

    public function logout(){
        $_id = multi_auth()->id;
        $userwa = UsersWhatsapp::where('user_id',$_id)->first();
        $userwa->delete();
        $this->set_logout();
        return response()->json(['message' => 'WhatsApp Logout Successfully'], 201);
    }

    protected function makeRequest($url, $method="GET", $params = []){
        try {
            $data = null;
            $response = Http::get($url, $params);
            if($method == "POST"){
                $response = Http::post($url, $params);
            }
            if($method == "DELETE"){
                $response = Http::delete($url);
            }
            if($method == "PATCH"){
                $response = Http::patch($url, $params);
            }
            if($method == "PUT"){
                $response = Http::put($url, $params);
            }
            if ($response->successful()) {
                $data = $response->json();
            }

            Storage::disk('local')->append('proses_whatsapp.txt', 'response => '.json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n");
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function hapus_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW').'/'.$_id;
        $params = [];
        $response = $this->makeRequest($url, "DELETE", $params);
        return $response;
    }


    protected function tambah_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW');
        $params = array(
            "_id" => $_id,
            "connectionUpdateWebhook" => env('APP_URL').'notification/whatsapp/receive_qr/'.multi_auth()->id,// route('notification.whatsapp.receive_qr'),
            "messagesUpsertWebhook" => env('APP_URL').'notification/whatsapp/receive_message/'.multi_auth()->id
        );
        $response = $this->makeRequest($url, "POST", $params);
        return $response;
    }


    protected function hentikan_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."close/".$_id;
        $response = $this->makeRequest($url, "POST");
        return $response;
    }


    protected function start_whatsapp(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."start/".$_id;
        $response = $this->makeRequest($url, "POST");
        return $response;
    }

    public function get_qr(){
        $_id = multi_auth()->id;
        $wa = UsersWhatsapp::where('user_id',$_id)->first();
        return response()->json($wa, 200);
    }

    public function get_data(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."/".$_id;
        $response = $this->makeRequest($url, "GET");
        Storage::disk('local')->append('proses_whatsapp.txt', 'Get Data => '.json_encode($response, JSON_PRETTY_PRINT) . "\n\n");
        return $response;
    }

    public function get_group(){
        $this->start_whatsapp();
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."store/contacts/".$_id;
        $response = $this->makeRequest($url, "GET");
        $rx = array(
            'url' => $url,
            'response' => $response
        );
        Log::info('get group wa');
        Log::info($response);
        $name =[];
        $user_id = multi_auth()->id;
        $wag = UserWhatsappGroup::where('user_id', $user_id);
        $wag->delete();
        foreach ($response as $key=>$val){
            if(Str::contains($val['id'], '@g.us')) {
                if(isset($val['name'])){
//                    $name[$val['id']]=$val['name'];
                    UserWhatsappGroup::create([
                        'group_id' => $val['id'],
                        'group_name' => $val['name'],
                        'user_id' => $user_id
                    ]);
                }
            }
        }
//        return response()->json($name, 200);
        return response()->json(['message' => 'WhatsApp Group Load Successfully'], 201);
    }

    public function set_logout(){
        $_id = multi_auth()->whatsapp."_".env('APP_ENV');
        $url = env('WHATSAPP_URL_NEW')."logout/".$_id;
        $response = $this->makeRequest($url, "POST");
        Storage::disk('local')->append('proses_whatsapp.txt', 'Get Data => '.json_encode($response, JSON_PRETTY_PRINT) . "\n\n");
        return $response;
    }

    public function getTemplate(Request $request)
    {
        $data = WatemplateDinetkan::where('shortname', multi_auth()->shortname)->first();
        return response()->json($data);
    }


}

<?php


namespace App\Http\Controllers\Callback;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersWhatsapp;
use App\Models\Whatsapp\Mpwa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsappCallbackController extends Controller
{
    public function receive_qr(Request $request, $userid = 0){
        if(isset($request->qr)){
            if($userid > 0){
                $user = User::where('id', $userid)->first();
                if($user){
                    $userwa = UsersWhatsapp::where('user_id', $userid)->first();
                    if(!$userwa){
                        $data = array(
                            'user_id' => $user->id,
                            'qr_url' => $request->qr
                        );
                        UsersWhatsapp::create($data);
                    }
                    if($userwa){
                        $data = array(
                            'qr_url' => $request->qr
                        );
                        $userwa->update($data);
                    }
                }
            } else {
                echo "Not FOund";exit;
            }
        }
        if(isset($request->connection) || isset($request->isOnline)){
            if($request->connection == "open" || $request->isOnline == true){

                if($userid > 0){
                    $user = User::where('id', $userid)->first();
                    if($user){
                        $userwa = UsersWhatsapp::where('user_id', $userid)->first();
                        if($userwa){
                            $data = array(
                                'is_login' => 1
                            );
                            $userwa->update($data);
                        }
                    }
                } else {
                    echo "Not FOund";exit;
                }
            }
        }
    }

    public function receive_qr_admin(Request $request, $userid = 0){
        Storage::disk('local')->append('receive_qr_admin.txt', "user_id ".$userid." => ".json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n");
        if(isset($request->qr)){
            if($userid > 0){
                $user = User::where('id', $userid)->first();
                if($user){
                    $userWA = Mpwa::where('shortname', $user->shortname)->first();
                    if($userWA){
                        $userWA = Mpwa::where('shortname', $user->shortname)->first();
                        $userWA->qr_url = $request->qr;
                        $userWA->save();
                    }
                }
            } else {
                echo "Not FOund";exit;
            }
        }
        if(isset($request->connection) || isset($request->isOnline)){
            if($request->connection == "open" || $request->isOnline == true){

                if($userid > 0){
                    $user = User::where('id', $userid)->first();
                    if($user){
                        $userwa = Mpwa::where('shortname', $user->shortname)->first();
                        if($userwa){
                            $userWA = Mpwa::where('shortname', $user->shortname)->first();
                            $userWA->is_login = 1;
                            $userWA->save();
                        }
                    }
                } else {
                    echo "Not FOund";exit;
                }
            }
            if($request->connection == "close" ){

                if($userid > 0){
                    $user = User::where('id', $userid)->first();
                    if($user){
                        $userwa = Mpwa::where('shortname', $user->shortname)->first();
                        if($userwa){
                            $userWA = Mpwa::where('shortname', $user->shortname)->first();
                            $userWA->is_login = 0;
                            $userWA->qr_url = "";
                            $userWA->save();
                        }
                    }
                } else {
                    echo "Not FOund";exit;
                }
            }
        }
    }
    public function receive_message(Request $request, $userid = 0){
        Storage::disk('local')->append('receive_messages.txt', json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n");
//        if(isset($request->qr)){
            if($userid > 0){
                $user = User::where('id', $userid)->first();
                if($user){
                    $userwa = UsersWhatsapp::where('user_id', $userid)->first();
//                    if(!$userwa){
//                        $data = array(
//                            'user_id' => $user->id,
//                            'qr_url' => $request->qr
//                        );
//                        UsersWhatsapp::create($data);
//                    }
//                    if($userwa){
//                        $data = array(
//                            'qr_url' => $request->qr
//                        );
//                        $userwa->update($data);
//                    }
                }
            } else {
                echo "Not FOund";exit;
            }
//        }
    }

}

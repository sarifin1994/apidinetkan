<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use App\Services\CustomMailerService;
use Illuminate\Http\Request;

class SmtpSettingController extends Controller
{
    public function index(Request $request)
    {
        $setting = SmtpSetting::where('shortname', multi_auth()->shortname)->first();
        if($setting == null){
            SmtpSetting::updateOrCreate(
                [
                    'shortname' => multi_auth()->shortname,
                    'host' => '',
                    'port' => '',
                    'encryption' => 'tls',
                    'username' => '',
                    'password' => ''
                ]
            );
        }
        $setting = SmtpSetting::where('shortname', multi_auth()->shortname)->first();
        return view('backend.setting.smtp.index_new', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'required|string|in:tls,ssl',
            'username' => 'required|string',
            'password' => 'required|string',
            'sender_name' => 'required|string',
        ]);

        SmtpSetting::updateOrCreate(
            ['shortname' => multi_auth()->shortname],
            $data
        );

//        return back()->with('success', 'SMTP disimpan.');
        return response()->json([
            'success' => true,
            'message' => 'SMTP disimpan.',
        ]);
    }

    public function test(Request $request){

        $user = $request->user();
        $data = [
            'notification' => 'Tetsing Notification',
            'user_name' => multi_auth()->shortname,
            'messages' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum'
        ];

        app(CustomMailerService::class)->sendWithUserSmtp(
            'emails.test',
            $data,
            $request->destination,
            'Tes SMTP Custom'
        );

        return "Email terkirim.";
    }
}

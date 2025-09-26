<?php

namespace App\Http\Controllers\Admin\Integration;

use App\Models\TelegramBot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TelegramController extends Controller
{
    public function index(Request $request)
    {
        $bot_token = env('TELEGRAM_BOT_TOKEN');
        $telegram = TelegramBot::where('group_id', $request->user()->id_group)->orderBy('id', 'asc')->get();
        return view('integrations.telegram.index', compact('telegram', 'bot_token'));
    }

    public function show(TelegramBot $telegram)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $telegram,
        ]);
    }

    public function update(Request $request, TelegramBot $telegram)
    {
        $validator = Validator::make($request->all(), [
            'chatid' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        $telegram->update([
            'chatid' => $request->chatid,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $telegram,
        ]);
    }
}

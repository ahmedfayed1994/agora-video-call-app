<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use TaylanUnutmaz\AgoraTokenBuilder\Role;

class AgoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('video_call', compact('users'));
    }

    public function generateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channelName' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $agoraToken = $this->generateAgoraToken($user->name, $request->channelName);

        return response()->json(['token' => $agoraToken, 'userName' => $user->name]);
    }

    private function generateAgoraToken($userName, $channelName)
    {
        $appId = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $expiredTime = time() + 3600;

        // استخدام Auth::id() للحصول على معرف المستخدم الحالي كـ UID
        $token = RtcTokenBuilder::buildTokenWithUid($appId, $appCertificate, $channelName, 0, 101, $expiredTime);

        return $token;
    }

}

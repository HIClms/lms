<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'country' => 'required',
            'state' => 'required',
            'address' => 'nullable|string',
            'city' => 'nullable',
            'bio' => 'nullable'
        ]);
        $user = User::find(Auth::id());
        if (!$user) {
            abort(400, 'User account not found');
        }
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->country = $request['country'];
        $user->address = $request['address'];
        $user->state = $request['state'];
        $user->city = $request['city'];


        if ($user->teacher) {
            $user->teacher->update(['bio' => $request['bio']]);
            if ($request->has('image')) {
                // $img = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();
                $img = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
                $user->profile_img = $img;
            }
        }
        $user->save();

        return response()->json($user->load('teacher'), 200);
    }

    public function activities()
    {
        $activity = Activity::where('user_id', Auth::id())->latest()->paginate(20);
        return $activity;
    }

    public function getAdminChats()
    {
        $chats = Chat::where([['user_id', '=', Auth::id()], ['sender_id', '=', 0]])->orWhere([['sender_id', '=', Auth::id()], ['user_id', '=', 0]])->get();
        return $chats;
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'user' => 'required',
            'message' => 'required|string',
        ]);

        if ($request->user != 0) {
            $user = DB::table('users')->where('uuid', $request->user)->first();
            if (!$user) {
                abort(400, 'User not found');
            }
        }

        $chat = new Chat();
        $chat->sender_id = Auth::id();
        $chat->user_id = 0;
        $chat->message = $request->message;
        $chat->save();

        return response()->json(['message' => 'Message sent', 'data' => $chat]);
    }
}

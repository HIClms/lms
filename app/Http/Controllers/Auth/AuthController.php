<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\VerificationMail;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function studentRegister(Request $request){
        $this->validate($request, [
            'firstname'=>'required|string',
            'lastname'=>'required|string',
            'username'=>'required|string|unique:users,username',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|confirmed',
        ]);

        $user = new User();
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->verify_token = Str::random(40);
        $user->password = bcrypt($request['password']);
        $user->save();

        $student = new Student();
        $student->user_id = $user->id;
        $student->save();

        $link = env('APP_FRONTEND').'/verify/'.$user->verify_token;

        // Mail::to($request['email'])->send(new VerificationMail($request['firstname'].' '.$request['lastname'], $link));

        return response()->json(['message'=>'Verificaton mail sent successfully'], 200);
    }

    public function login(Request $request){
        $user = $request->validate([
            'username'=>'required|string',
            'password'=>'required|string'
        ]);

        if(!Auth::attempt($user))
        {
            abort(401,"Invalid login credentials");
        }

        if(Auth::user()->role_id == 1){
            $accessToken = Auth::user()->createToken('access_token', ['student']);
        }else{
            $accessToken = Auth::user()->createToken('access_token', ['teacher']);
        }

        return response()->json(['token'=>$accessToken->plainTextToken, 'user'=>new UserResource(Auth::user())]);
    }

    public function teacherRegister(Request $request){
        $this->validate($request, [
            'firstname'=>'required|string',
            'lastname'=>'required|string',
            'username'=>'required|string|unique:users,username',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|confirmed',
        ]);

        $user = new User();
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->role_id = 2;
        $user->verify_token = Str::random(40);
        $user->password = bcrypt($request['password']);
        $user->save();

        $teacher = new Teacher();
        $teacher->user_id = $user->id;
        $teacher->save();

        $link = env('APP_FRONTEND').'/verify/'.$user->verify_token;

        // Mail::to($request['email'])->send(new VerificationMail($request['firstname'].' '.$request['lastname'], $link));

        return response()->json(['message'=>'Verificaton mail sent successfully'], 200);
    }

    public function emailVerify(Request $request){
        $this->validate($request, [
            'token'=>'required|string'
        ]);
        $user = User::where('verify_token', $request['token'])->first();
        if(!$user){
            abort(400,'Invalid token, please try again');
        }
        $user->verify_status = 1;
        $user->save();

        return response()->json(['message'=>'Email verified successfully'], 200);
    }
}

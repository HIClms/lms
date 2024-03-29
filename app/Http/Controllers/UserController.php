<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateProfile(Request $request){
        $this->validate($request, [
            'firstname'=>'required|string',
            'lastname'=> 'required|string',
            'country'=>'required',
            'state'=>'required',
            'address'=>'nullable|string',
            'city'=>'nullable',
            'bio'=>'nullable'
        ]);
        $user = User::find(Auth::id());
        if(!$user){
            abort(400,'User account not found');
        }
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->country = $request['country'];
        $user->address = $request['address'];
        $user->state = $request['state'];
        $user->city = $request['city'];


        if($user->teacher){
            $user->teacher->update(['bio'=>$request['bio']]);
            if($request->has('image')){
                // $img = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();
                $img = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
                $user->profile_img = $img;
            }
        }
        $user->save();

        return response()->json($user->load('teacher'), 200);
    }
}

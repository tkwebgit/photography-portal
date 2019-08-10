<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
        ]);

        $attributes['password']=Hash::make($attributes['password']);
        $usercreated = User::create($attributes);
        return response([
            'status'=>true,
            'user'=>$usercreated
        ],200);

    }
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        $user = User::where('email', $request->email)->first();
        if($user === null){
            return response([
                'status'=>false,
                'message'=>'user not found'
            ],404);
        }
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('photoapp');
            return response([
                'status'=>true,
                'access_token'=> $token->accessToken,
                'message'=> 'Successfully logged in!!',
                'user'=> $user
            ],200);
        } else {
            return response([
                'status'=>false,
                'message'=>'Invalid credentials, try again.'
            ],401);
        }
    }
    public function logout()
    {
        if (Auth::check()) {
            $accessToken = Auth::user()->token();
            $user = Auth::user()->AauthAcessToken()->where('id',$accessToken->id)->first()->delete();
            return response([
                'status'=>true,
                'message'=>'Successfully logged out'
            ],200);
         }

        return response()->json('Logged out successfully', 200);
    }
}

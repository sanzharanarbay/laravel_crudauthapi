<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PassportController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('CrudAuthApi')->accessToken;


        return response()->json(['token' => $token], 200);
    }

      //Login
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('CrudAuthApi')->accessToken;
           // $token->expires_at = $request->remember_me ?
              //  Carbon::now()->addMonth() :
              //  Carbon::now()->addDay();

            //$token->save();
            return response()->json([
                'token' => $token
                //'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
            ], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'You are successfully logged out',
        ]);
    }

    //User Details
    public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
}

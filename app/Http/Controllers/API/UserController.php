<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    //

    public function login(Request $request){
        try{
            $request->validate([
                'email'=> 'email|required',
                'password'=> 'required'
            ]);

            $credential = request(['email','password']);
            if(!Auth::attempt($credential)){
                return ResponseFormatter::error([
                    'message'=>'Unauthorized',
                    
                ],'Authentication failed',500);
            } 

            $user = User::where('email',$request->email)->first();

            if(!Hash::check($request->password, $user->password,[])){
                throw new Exception('Invalid Credential');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token'=> $tokenResult,
                'token_type'=> 'Bearer',
                'user'=>$user
            ],'Authenticated');
        }
        catch(Exception $error){
            return ResponseFormatter::error([
                'message'=>'Something went wrong',
                'error'=>$error
            ],'Authentication failed',500);
        }
    }
    public function register(Request $request){
        try{
            $request->validate([
                'name'=>['required','string','max:255'],
                'username'=>['required','string','max:255','unique:users'],
                'phone_number'=>['nullable','string','max:255'],
                'email'=>['required','string','email','max:255','unique:users'],
                'password'=>['required','string',new Password],
            ]);

            User::create([
                'name'=> $request->name,
                'username'=>$request->username,
                'phone_number'=>$request->phone_number,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);

            $user = User::where('email',$request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token'=>$tokenResult,
                'token_type'=>'Bearer',
                'user'=>$user
            ],'User sudah terdaftar');
        }
        catch(Exception $eror){
            return ResponseFormatter::error([
                'message'=>'Something went wrong',
                'error'=>$eror,
            ],'Authentication Error',500);
        }
    }

    public function fetch(Request $request){
        return ResponseFormatter::success($request->user(),'Data profile User berhasil diambil');
    }

    public function updateProfile(Request $request){
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user,'Profile Updated');
    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token,'Token Revoked');
    }
}

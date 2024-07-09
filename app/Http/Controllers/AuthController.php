<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function Register_to_backoffice(Request $request){
        
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);
        
        if($user){
            return response()->json(["message" => "User created successfully"]);
        }

        return response()->json(["message" => "error while creating User"]);
        
    }



    public function generateToken($data,$id){

        $secret_key = env('JWT_SECRET');
        $issuer_claim = "localhost";
        $audience_claim = "localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 36000;

        $token = [
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "sub" => $id,
            "data" => [
                "name" => $data['name'],
                'email' => $data['email'],
            ]
        ];

        $jwt = JWT::encode($token, $secret_key, 'HS256');

        return $jwt;
    }

    public function login_admin (Request $request){

        //login validation
        $data = $request->validate([
            'name' => 'required',
            'password' => 'required',
            'email' => ['required','email']
        ]);

        //login test
        $token = auth()->attempt([
            "name" => $data['name'],
            'password' => $data['password'],
            'email' => $data['email']]
        );
        
        $user = auth()->user();
        $user_id = $user->id;
        
        //login fail
        if(!$token){
            return response()->json(["message" => "User not found"]);
        }

        $jwt = $this->generateToken($data,$user_id);

        //login success
        return response()->json([
            "message" => "User logged in successfully",
            "token" => $jwt
        ]);

    }

    public function ChangePassword(Request $request,int $id){

        $data = $request->validate([
            "current_pwd" => 'required',
            'new_password' => ['required','min:8']
        ]);

        $user = User::find($id);
        

        if (!$user) {
            return response()->json(["message" => "User not authenticated"]);
        }

        if(!Hash::check($data['current_pwd'] , $user->password)){
            return response()->json(["message" => "wrong password"]);
        }

        /** @var \App\Models\User $user **/
        $user->password = Hash::make($data['new_password']);
        $user->save();

        return response()->json(["message" => "Password changed successfully"]);

        
    }
}

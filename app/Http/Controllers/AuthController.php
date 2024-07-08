<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function Register_to_backoffice(Request $request){

        
    }

    public function login_admin (Request $request){

        //login validation
        $data = $request->validate([
            'name' => 'required',
            'password' => 'required',
            'email' => ['required','email']
        ]);

        //login test
        if(auth()->attempt(['name' => $data['name'] , 'password' => $data['password'] , 'email' => $data['email']])){
            return response()->json(["message" => "logged in successfully" , "admin" => $data]);
        }

        //login fail
        return response()->json(["message" => "User not found"]);

    }
}

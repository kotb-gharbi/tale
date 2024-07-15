<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function AddUser(Request $request){
        
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string'],
            'roles' => ['required', 'array'],
            'roles.*' => 'exists:roles,name'
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);
        
        if($user){
            
            $roleIds = Role::whereIn('name', $data['roles'])->pluck('id')->toArray();

            $user->roles()->attach($roleIds);

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

    public function EditRoles(Request $request,$id){

        $data = $request->validate([
            'roles' => ['required','array'],
            'roles.*' => 'exists:roles,name'
        ]);

        $roleIds = Role::whereIn('name' , $data['roles'])->pluck('id')->toArray();

        $user = User::find($id);

        if($user){
            $user->roles()->sync($roleIds);

            return response()->json(["message" => "Roles updated successfully"]);
        }

        return response()->json(["message" => "User not found"]);
        

    }

    public function DeleteUser($id){

        $user = User::find($id);

        if($user){

            $user->delete();

            //detach all roles associated with that user
            $user->roles()->detach();

            return response()->json(["message" => "User deleted successfully"]);
        }

        return response()->json(["message" => "User not found"]);
    }

    public function DeactivateUser($id){

        $user = User::find($id);

        if($user){

            //detach all roles
            $user->roles()->detach();

            return response()->json(["message" => "All roles removed from user successfully."]);
        }

        return response()->json(["message" => "User not found."]);
        
    }

    public function EditUser(Request $request, int $id){

        $user = User::find($id);
        
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['required', 'string'],
            'roles' => ['required', 'array'],
            'roles.*' => 'exists:roles,name'
        ]);

        $data['password'] = bcrypt($data['password']);


        if(!$user){
            return response()->json(["message" => "User not found"]);
        }

        $roleIds = Role::whereIn('name' , $data['roles'])->pluck('id')->toArray();
        
        $user->roles()->sync($roleIds);

        $user->update($data);

        return response()->json(["message" => "User updated successfully"]);

    }
}

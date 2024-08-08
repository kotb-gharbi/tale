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
            'last_name' => ['required' ,'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required' , "string"],
            'status' => ['nullable', 'boolean'],
            'birth' => ['date', 'nullable'],
            'gender' => ['required' , 'in:male,female'],
            'country' => ['string' , 'nullable'],
            'tel' => ['string' , 'nullable'],
            'address' => ['string' , 'nullable'],
            'CodePostal' => ['string' , 'nullable'],
            'profile_pic' => ['nullable', 'url'],
            'roles' => ['required', 'array'],
            'roles.*' => 'exists:roles,name'
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);
        
        if($user){
            
            $roleIds = Role::whereIn('name', $data['roles'])->pluck('id')->toArray();

            $user->roles()->attach($roleIds);

            return response()->json(["message" => "User created successfully", "status" => true]);
        }

        return response()->json(["message" => "error while creating User" , "status" => false]);
        
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
                "id" => $id,
            ]
        ];

        $jwt = JWT::encode($token, $secret_key, 'HS256');

        return $jwt;
    }

    public function SuperAdminLogin (Request $request){

        //login validation
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required' , "string"],
        ]);

        $user = User::where('email' , $data['email'])->first();

        if(!$user){
            return response()->json(["message" => "Email incorrect" , "status" => false]);
        }

        if(!auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ])) {
            return response()->json(["message" => "Password incorrect" , "status" => false]);
        }

        //get super_admin role_id
        $roleId = Role::whereIn('name', ['super_admin'])->pluck('id')->toArray();
        //get user roles_ids
        $userRoleIds = $user->roles->pluck('id')->toArray();
        //check if user has super_admin role
        if (!array_intersect($roleId, $userRoleIds)) {
            return response()->json(["message" => "User does not have the required role", "status" => false]);
        }

        $user_id = auth()->user()->id;
        
        $jwt = $this->generateToken($data,$user_id);

        //login success
        return response()->json([
            "message" => "User logged in successfully",
            "token" => $jwt,
            "status" => true
        ]);

    }

    public function ChangePassword(Request $request,int $id){

        $data = $request->validate([
            'password' => ['required','min:8']
        ]);

        $user = User::find($id);
        

        if (!$user) {
            return response()->json(["message" => "User not authenticated" , "status" => false]);
        }


        /** @var \App\Models\User $user **/
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(["message" => "Password changed successfully" , "status" => true]);

        
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

            return response()->json(["message" => "Roles updated successfully" , "status" => true]);
        }

        return response()->json(["message" => "User not found" , "status" => false]);
        

    }

    public function DeleteUser($id){

        $user = User::find($id);

        if($user){

            $user->delete();

            //detach all roles associated with that user
            $user->roles()->detach();

            return response()->json(["message" => "User deleted successfully" , "status" => true]);
        }

        return response()->json(["message" => "User not found" , "status" => false]);
    }

    public function DeactivateUser($id){

        $user = User::find($id);

        if($user){

            //deactivate user
            $user->status = false;
            $user->save();

            return response()->json(["message" => "User deactivated" , "status" => true]);
        }

        return response()->json(["message" => "User not found." , "status" => false]);
        
    }
    public function ActivateUser($id){

        $user = User::find($id);

        if($user){

            //activate user
            $user->status = true;
            $user->save();

            return response()->json(["message" => "User deactivated" , "status" => true]);
        }

        return response()->json(["message" => "User not found." , "status" => false]);
        
    }

    public function EditName(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'name' => ['required' ,'string']
        ]);

        $user->name = $data['name'];
        $user->save();

        return response()->json(['message' => 'User name updated successfully' , 'status' => true]);



        
    }
    public function EditLastName(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'last_name' => ['required' ,'string']
        ]);

        $user->last_name = $data['last_name'];
        $user->save();

        return response()->json(['message' => 'User last_name updated successfully' , 'status' => true]);

    }
    public function EditBirthDate(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'birth' => ['required' ,'date']
        ]);

        $user->birth = $data['birth'];
        $user->save();

        return response()->json(['message' => 'User Birth date updated successfully' , 'status' => true]);

        
    }
    public function EditGender(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'gender' => ['required' ,'in:male,female']
        ]);

        $user->gender = $data['gender'];
        $user->save();

        return response()->json(['message' => 'User gender updated successfully' , 'status' => true]);

        
    }
    public function EditEmail(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'email' => ['required','string','email' , 'unique:users']
        ]);

        $user->email = $data['email'];
        $user->save();

        return response()->json(['message' => 'User email updated successfully' , 'status' => true]);

        
    }
    public function EditCountry(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'country' => ['required' ,'string']
        ]);

        $user->country = $data['country'];
        $user->save();

        return response()->json(['message' => 'User country updated successfully' , 'status' => true]);

        
    }
    public function EditTel(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'tel' => ['required' ,'string']
        ]);

        $user->tel = $data['tel'];
        $user->save();

        return response()->json(['message' => 'User phone number updated successfully' , 'status' => true]);

        
    }
    public function EditAddress(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'address' => ['required' ,'string']
        ]);

        $user->address = $data['address'];
        $user->save();

        return response()->json(['message' => 'User address updated successfully' , 'status' => true]);

        
    }
    public function EditCodePostal(Request $request, int $id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found." , "status" => false]);
        }

        $data = $request->validate([
            'CodePostal' => ['required' ,'string']
        ]);

        $user->CodePostal = $data['CodePostal'];
        $user->save();

        return response()->json(['message' => 'User CodePostal updated successfully' , 'status' => true]);

        
    }

    function GetUser($id){

        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not found" , "status" => false]);
        }

        return response()->json($user);
    }

    function GetAllUsers(){

        $roleId = Role::where('name', 'super_admin')->pluck('id')->first();
        //get all users who don't have super_admin role
        $users = User::whereDoesntHave('roles', function($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->get();
        
        
        if(!$users){
            return response()->json(["message" => "No Users found" , "status" => false]);
        }

        return response()->json($users);
    }
}

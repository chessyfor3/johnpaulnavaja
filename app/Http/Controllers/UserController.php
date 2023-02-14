<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFacility;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function list() 
  {
    return User::all();
  }

  public function authenticate(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'username' => 'required',
      'password' => 'required'
    ]);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Invalid login credentials',
      ]);
    }
    $user = User::where('username', $request->username)->first();
    if(!$user){
      return response()->json([
        'status' => 'fail',
        'message'=> 'Invalid login credentials',
      ]);
    }
    if(Hash::check($request->password, $user->password)){
      $apikey = base64_encode($this->v4());
      User::where('username', $request->username)->update(['api_token' => $apikey]);
      $user->api_token = $apikey;
      return response()->json([
        'status' => 'success',
        'user' => $user,
      ]);
    }else{
      return response()->json([
        'status' => 'fail',
        'errors'=> [ 'username' => ['Invalid login credentials'] ]
      ], 401);
    }
  }

  public function store(Request $request)
  {
    $rules = [
      'username' => 'required|string|min:8|max:16|unique:users',
      'name' => 'required|string',
      'password' => 'required|min:8',
    ];
    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Please check the required fields.',
        'errors' => $validator->errors()
      ]);
    }

    $apikey = base64_encode($this->v4());
    $user = User::create([
      'username' => $request->username,
      'name' => $request->name,
      'api_token' => $apikey,
      'password' => Hash::make($request->password)
    ]);

    if(!$user){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Error in saving a new user record.',
      ]);
    }

    if($request->roles && is_array($request->roles)){
      foreach($request->roles as $role){
        UserRole::create([
          'user_id' => $user->id,
          'role_id' => $role['role_id'],
        ]);
      }
    }

    if($request->facilities && is_array($request->facilities)){
      foreach($request->facilities as $facility){
        UserFacility::create([
          'user_id' => $user->id,
          'facility_id' => $facility['facility_id'],
        ]);
      }
    }

    return response()->json([
      'status' => 'success',
      'message' => 'User created',
    ]);
  }

  public function getUser($id)
  {
    $user = User::with('roles')->find($id);
    if(!$user){
      return response()->json([
        'status' => 'fail', 
        'message' => 'User not found!',
      ], 406);
    }
    return response()->json([
      'status' => 'success', 
      'user' => $user,
    ]);
  }

  public function update($id, Request $request)
  {
    $rules = [
      'username' => "required|string|min:8|max:16|unique:users,id,$id",
      'name' => 'required|string',
      'password' => 'required|min:8',
    ];
    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Please check the required fields.',
        'errors' => $validator->errors()
      ]);
    }
    $user = User::find($id);
    if (!$user) {
      return response()->json(['status' => 'fail', 'message' => 'User not found!'],401);
    }

    $user->username = $request->username;
    $user->name = $request->name;
    $user->password = Hash::make($request->password);
    if ($user->save()) {
      $roles = [];
      $facilities = [];
      if ($request->roles && is_array($request->roles)) {
        foreach($request->roles as $role){
          $roles[$role['role_id']] = $role['role_id'];
          if(!UserRole::where("user_id", $user->id)->where('role_id', $role['role_id'])->first()){
            UserRole::create([
              'user_id' => $user->id,
              'role_id' => $role['role_id'],
              'createdby' => Auth::user()->id,
            ]);
          }
        }
      }

      foreach($user->user_roles as $role){
        if(!in_array($role->role_id, $roles)){
          $role->delete();
        }
      }

      if ($request->facilities && is_array($request->facilities)) {
        foreach($request->facilities as $facility){
          $facilities[$facility['facility_id']] = $facility['facility_id'];
          if(!UserFacility::where("user_id", $user->id)->where('facility_id', $facility['facility_id'])->first()){
            UserFacility::create([
              'user_id' => $user->id,
              'facility_id' => $facility['facility_id'],
              'createdby' => Auth::user()->id,
            ]);
          }
        }
      }

      foreach($user->user_facilities as $facility){
        if(!in_array($facility->facility_id, $facilities)){
          $facility->delete();
        }
      }

      return response()->json(['status' => 'success','message' => 'User updated!']);
    }
  }

  public function delete($id)
  {
    $user = User::find($id);
    if (!$user) {
      return response()->json(['status' => 'fail', 'message' => 'User not found!'],406);
    }
    $user->user_roles()->delete();
    $user->user_facilities()->delete();
    $user->delete();
    return response()->json(['status' => 'success','message' => 'User deleted!']);
  }

  protected function v4() 
  {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }
}

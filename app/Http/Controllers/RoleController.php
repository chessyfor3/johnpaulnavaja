<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
  public function list()
  {
    return Role::all();
  }

  public function store(Request $request)
  {
    $rules = [
      'name' => 'required|string|max:200|unique:roles',
    ];
    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Please check the required fields.',
        'errors' => $validator->errors()
      ]);
    }
    $role = Role::create([
      'name' => $request->name,
    ]);
    if (!$role) {
			return response()->json([
        'status'=> 'fail', 
        'message' => 'Error in saving a new role.',
      ]);
		}
		return response()->json([
      'status' => 'success',
      'message' => 'New role created',
    ]);
  }

  public function getRole($id)
  {
    $role = Role::find($id);
		if(!$role){
			return response()->json([
				'status' => 'fail', 
				'message' => 'Role not found!',
			], 406);
		}
		return response()->json([
			'status' => 'success', 
			'role' => $role,
		]);
  }
  
  public function update($id, Request $request)
  {
    $rules = [
      'name' => "required|string|max:200|unique:roles,id,$id",
    ];
    $validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Please check the required fields.',
        'errors' => $validator->errors()
      ]);
    }
    $role = Role::find($id);
    $role->name = $request->name;
    if ($role->save()) {
			return response()->json([
				'status' => 'success',
				'message' => 'Role information updated',
			]);
		}
		
		return response()->json([
			'status'=> 'fail', 
			'message' => 'Error in saving the role information.',
		]);
  }

  public function delete($id)
  {
    $role = Role::find($id);
    $role->user_roles()->delete();
    $role->delete();
    return response()->json(['status' => 'success','message' => 'Role deleted!']);
  }
}

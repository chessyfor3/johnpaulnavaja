<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilityController extends Controller
{
	public function list()
	{
		return Facility::all();
	}

	public function store(Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'address' => 'required|string|max:200',
			'city' => 'required|string|max:200',
			'state' => 'required|string|max:100',
			'zip' => 'required|string|max:10',
			'contact' => 'required|string|max:100'
		];
		$validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Please check the required fields.',
        'errors' => $validator->errors()
      ]);
    }
		$facility = Facility::create([
			'name' => $request->name,
			'address' => $request->address,
			'city' => $request->city,
			'state' => $request->state,
			'zip' => $request->zip,
			'contact' => $request->contact,
		]);

		if (!$facility) {
			return response()->json([
        'status'=> 'fail', 
        'message' => 'Error in saving a new facility record.',
      ]);
		}
		return response()->json([
      'status' => 'success',
      'message' => 'Facility created',
    ]);
	}

	public function getFacility($id)
	{
		$facility = Facility::find($id);
		if(!$facility){
			return response()->json([
				'status' => 'fail', 
				'message' => 'Facility not found!',
			], 406);
		}
		return response()->json([
			'status' => 'success', 
			'facility' => $facility,
		]);
	}
	
	public function update($id, Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'address' => 'required|string|max:200',
			'city' => 'required|string|max:200',
			'state' => 'required|string|max:100',
			'zip' => 'required|string|max:10',
			'contact' => 'required|string|max:100'
		];
		$validator = Validator::make($request->all(), $rules);
    if($validator->fails()){
      return response()->json([
        'status'=> 'fail', 
        'message' => 'Please check the required fields.',
        'errors' => $validator->errors()
      ]);
    }
		$facility = Facility::find($id);
		$facility->name = $request->name;
		$facility->address = $request->address;
		$facility->city = $request->city;
		$facility->state = $request->state;
		$facility->zip = $request->zip;
		$facility->contact = $request->contact;

		if ($facility->save()) {
			return response()->json([
				'status' => 'success',
				'message' => 'Facility updated',
			]);
		}
		
		return response()->json([
			'status'=> 'fail', 
			'message' => 'Error in saving the facility record.',
		]);
	}

	public function delete($id)
	{
		$facility = Facility::find($id);
		$facility->user_facilities()->delete();
		$facility->delete();
		return response()->json(['status' => 'success','message' => 'Facility deleted!']);
	}
}

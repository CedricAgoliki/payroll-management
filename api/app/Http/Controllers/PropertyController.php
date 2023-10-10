<?php

namespace App\Http\Controllers;

use App\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    public function all()
    {
	return Property::all();
    }

    public function newProperty(Request $request)
    {
	$property = new Property();
	$property->name = $request->post('name');
	$property->description = $request->post('description');
	$property->save();
	return response('{"success": true}', 200);
    }

    public function modifyProperty($id, Request $request)
    {
	$property = Property::find($id);
	if ($property !== null) {
	    $property->name = $request->post('name');
	    $property->description = $request->post('description');
	    $property->save();
	    return response('{"success": true}', 200);
	}
	return response('{"success": false}', 200);
    }

    public function deleteProperty($id)
    {
	$property = Property::find($id);
	if ($property !== null) {
	    $property->delete();
	    return response('{"success": true}', 200);
	}
	return response('{"success": false}', 200);
    }

    public function massDelete(Request $request)
    {
	$ids = json_decode($request->post('ids'));
	foreach($ids as $id) {
	    $property = Property::find($id);
	    if ($property !== null) $property->delete();
	}
	return response('{"success": true}', 200);
    }

    public function assignValue(Request $request)
    {
	// id of employee and property are sent along with the value
    }
}

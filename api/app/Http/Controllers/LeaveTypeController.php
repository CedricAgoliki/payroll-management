<?php

namespace App\Http\Controllers;

use App\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function newLeaveType(Request $request)
    {
	$type = new LeaveType();
	$type->label = $request->post('label');
	$type->deduct = $request->post('deduct');
	if ($request->post('description')) {
	    $type->description = $request->post('description');
	}
	if ($type->save()) {
	    return response('{"success": true, "reason": "saved" }', 200);
	}
	return response('{"success": false, "reason": "Error" }', 200);
    }

    public function leaveTypeList()
    {
	return LeaveType::all();
    }

    public function modifyLeaveType(int $id, Request $request)
    {
	$type = LeaveType::find($id);
	if ($type !== null) {
	    $type->label = $request->post('label');
	    $type->deduct = $request->post('deduct');
	    if ($request->post('description')) {
		$type->description = $request->post('description');
	    }
	    if ($type->save()) {
		return response('{"success": true, "reason": "saved" }', 200);
	    }
	}
	return response('{"success": false, "reason": "Error" }', 200);
    }

    public function deleteLeaveType(int $id)
    {
	$type = LeaveType::find($id);
	if ($type !== null) {
	    $type->delete();
	    return response('{"success": true, "reason": "deleted" }', 200);
	}

	return response('{"success": false, "reason": "Not found" }', 200);
    }

    public function massDelete() {
	$ids = json_decode($request->post('ids'));
	foreach($ids as $id) {
	    $type = LeaveType::find($id);
	    if ($type !== null) $type->delete();
	}
	return response('{"success": true}', 200);
    }
}

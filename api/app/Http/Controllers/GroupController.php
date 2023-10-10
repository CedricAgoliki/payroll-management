<?php

namespace App\Http\Controllers;

use App\Group;
use App\Element;
use App\Employee;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function newGroup(Request $request)
    {
	$data = json_decode($request->post('data'), true);
	$group = new Group();
	$group->label = $data['label'];
	$group->seniority_bonus = $data['allowed']['seniority'];
	if ($group->save()) {
	    foreach ($data['members'] as $id) {
		$employee = Employee::find($id);
		if ($employee !== null) {
		    $employee->group()->associate($group);
		    $employee->save();
		}
	    }

	    foreach ($data['primes'] as $key => $value) {
		$element = Element::find($key);
		if ($element !== null) {
		    $element->groups()->attach($group, [
			'value' => $value,
			'allowed' => $data['allowed'][$key],
			'variable' => $data['variable'][$key]
		    ]);
		}
	    }

	    foreach ($data['deductions'] as $key => $value) {
		$element = Element::find($key);
		if ($element !== null) {
		    $element->groups()->attach($group, [
			'value' => $value,
			'allowed' => $data['allowed'][$key],
			'variable' => $data['variable'][$key]
		    ]);
		}
	    }
	    return response('{"success": true, "reason": "saved"}', 200);
	}

	return response('{"success": false, "reason": "Employee not saved"}', 200);
    }


    /*public function groupListWithAttributes()
    {
	return $group = Group::with('elements')->with('employees')->get();
    }*/

    public function groupDetails(int $id)
    {
	return Group::with('elements')->with('employees')
	    ->where('id', $id)->first();
    }

    public function groupList()
    {
	return Group::all();
    }

    public function deleteGroup(int $id)
    {
	$group = Group::find($id)->first();
	if ($group !== null) {
	    $group->elements()->detach();
	    foreach ($group->employees as $emp) {
		$emp->group_id = null;
		$emp->save();
	    }
	    $group->delete();
	    return response('{"success": true, "reason": "Deleted"}', 200);
	} 
	return response('{"success": false, "reason": "group not found"}', 200);
    }
    
    public function updateGroup(Request $request)
    {
	$data = json_decode($request->post('data'), true);
	$group = Group::find($data['groupId']);
	if ($group !== null) {
	    $group->label = $data['label'];
	    $group->seniority_bonus = $data['allowed']['seniority'];

	    //remove old members
	    $members = $group->employees;
	    foreach ($members as $emp) {
		$emp->group_id = null;
		$emp->save();
	    }

	    // add new members
	    foreach ($data['members'] as $id) {
		$employee = Employee::find($id);
		if ($employee !== null) {
		    $employee->group()->associate($group);
		    $employee->save();
		}
	    }

	    //remove old elements
	    $group->elements()->detach();

	    //updating elements
	    foreach ($data['primes'] as $key => $value) {
		$element = Element::find($key);
		if ($element !== null) {
		    $element->groups()->attach($group, [
			'value' => $value,
			'allowed' => $data['allowed'][$key],
			'variable' => $data['variable'][$key]
		    ]);
		}
	    }

	    foreach ($data['deductions'] as $key => $value) {
		$element = Element::find($key);
		if ($element !== null) {
		    $element->groups()->attach($group, [
			'value' => $value,
			'allowed' => $data['allowed'][$key],
			'variable' => $data['variable'][$key]
		    ]);
		}
	    }

	    if ($group->save()) return response('{"success": true, "reason": "updated"}', 200);
	    else response('{"success": false, "reason": "Group not saved"}', 200);
 
	}
	
	return response('{"success": false, "reason": "Employee not found"}', 200);

    }

}

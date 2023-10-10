<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends Controller
{
    public function addUser(Request $request)
    {
	$user = new User();
	$user->name = $request->post('name');
	$user->email = $request->post('email');
	$user->password = Hash::make("secret");
	$user->roles = $request->post('roles');
	if ($user->save()) {
	    return response('{"success": true, "reason": "user saved"}', 200);
	}
	return response('{"success": false, "reason": "error"}', 200);
    }

    public function modifyUser(int $id, Request $request)
    {
	//this function only modify user roles
	$user = User::find($id);
	if ($user !== null) {
	    $user->roles = $request->post('roles');
	    $user->save();
	    return response('{"success": true, "reason": "user saved"}', 200);
	}
	return response('{"success": false, "reason": "error"}', 200);
    }

    public function deleteUser(int $id)
    {
	$user = User::find($id);
	if ($user !== null) {
	    if ($user->delete()) {
		return response('{"success": true, "reason": "user deleted"}', 200);
	    }
	    return response('{"success": true, "reason": "an error occured"}', 200);
	}

	return response('{"success": true, "reason": "user not found"}', 200);
    }

    public function userList() {
	return User::all();
    }

    public function changeFirstPassword(int $id, Request $request)
    {
	$user = User::find($id);
	if ($user !== null) {
	    $user->password = Hash::make($request->post('password'));
	    $user->default_password = false;
	    if ($user->save()) {
		return response('{"success": true, "reason": "user saved"}', 200);
	    }
	}
	return response('{"success": false, "reason": "error"}', 200);
    }

    public function changePassword(int $id, Request $request)
    {
	$user = User::find($id);
	$plain = $request->post('current');
	if ($user !== null && Hash::check($plain, $user->password)) {
	    $user->password = Hash::make($request->post('password'));
	    $user->default_password = false;
	    if ($user->save()) {
		return response('{"success": true, "reason": "user saved"}', 200);
	    }
	}
	return response('{"success": false, "reason": "error"}', 200);
    }

    public function changeEmail(int $id, Request $request)
    {
	$user = User::find($id);
	if ($user !== null) {
	    $email = $request->post('email');
	    $user->email = $email;
	    if ($user->save()) {
		return response('{"success": true, "reason": "user saved"}', 200);
	    }
	}
	return response('{"success": false, "reason": "error"}', 200);	
    }

}

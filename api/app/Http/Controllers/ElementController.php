<?php

namespace App\Http\Controllers;

use App\Element;
use Illuminate\Http\Request;

class ElementController extends Controller
{

    public function primes()
    {
	return Element::where('type', '+')->get();
    }


    public function deductions()
    {
	return Element::where('type', '-')->get();
    }

    public function newElement(Request $request)
    {
	$element = new Element();
	$element->name = $request->post('name');
	$element->type = $request->post('type');
	$element->prorata = $request->post('prorata');
	$element->irpp = $request->post('prorata');
	$element->description = $request->post('description');
	$element->save();
	return response('{"success": true}', 200);
    }

    public function modifyElement($id, Request $request) {
	$element = Element::find($id);
	if ($element !== null) {
	    $element->name = $request->post('name');
	    $element->prorata = $request->post('prorata');
	    $element->description = $request->post('description');
	    $element->save();
	    return response('{"success": true}', 200);
	}
	return response('{"success": false}', 200);
    }

    public function deleteElement($id)
    {
	$element = Element::find($id);
	if ($element !== null) {
	    $element->delete();
	    return response('{"success": true}', 200);
	}
	return response('{"success": false}', 200);
    }

    public function massDelete(Request $request)
    {
	$ids = json_decode($request->post('ids'));
	foreach($ids as $id) {
	    $element = Element::find($id);
	    if ($element !== null) $element->delete();
	}
	return response('{"success": true}', 200);
    }
}

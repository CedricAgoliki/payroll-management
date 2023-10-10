<?php

namespace App\Http\Controllers;

use App\Advance;
use App\Employee;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{

    public function newAdvance(Request $request)
    {
	$employee = Employee::find($request->post('employee'));
	if ($employee) {
	    $base_salary = $employee->base_salary;
	    $advance_amount = $request->post('amount');
	    $advance_taken = Advance::where('employee_id', $employee->id)
				      ->where('paid', false)->get();
	    if ($base_salary > $advance_amount && sizeof($advance_taken) == 0) {
		$advance = new Advance();
		$advance->amount = $advance_amount;
		$advance->employee_id = $request->post('employee');
		$advance->date = $request->post('date');
		$advance->payment_date = $request->post('paymentDate');
		$advance->commentary = $request->post('commentary');
		$advance->type = $request->post('type');
		$advance->paid = false;
		$advance->cancelled = false;
		if ($advance->save()) {
		    return response('{"success": true}', 200);
		}
	    }
	}
	return response('{"success": false}', 200);
    }

    public function actualAdvanceCount()
    {
	return Advance::where([
	    ['type', '<>', 'loan'],
	    ['paid', false],
	    ['cancelled', false],
	])->count();
    }

    public function actualAdvanceList()
    {
	return Advance::where([
	    ['paid', false],
	    ['cancelled', false],
	])->with('employee')->get();
    }

    public function deleteAdvance(int $id, Request $request)
    {
	$advance = Advance::find($id);
	if ($advance !== null) {
	    $advance->cancelled = true;
	    $advance->cancellation_date = $request->post('date');
	    $advance->cancellation_reason = $request->post('reason');
	    if ($advance->save()) return response('{"success": true}', 200);
	    else return response('{"sucess": false}', 200);
	}
	return response('{"sucess": false}', 200);
    }

    public function loanPaid($id)
    {
	$loan = Loan::find($id);
	$loan->paid = true;
	if ($loan->save()) return response('{"success": true}', 200);
	return response('{"sucess": false}', 200);
    }
    
}

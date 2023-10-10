<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\Employee;
use App\Repayment;

class LoanController extends Controller
{
    public function newLoan(Request $request)
    {
	$employee_id = $request->post('employee');
	$commentary = $request->post('commentary');
	if (Employee::find($employee_id)) {

	    $loan = Loan::where('employee_id', $employee_id)
		->orderBy('created_at','desc')->first();
	    if ($loan && !$loan->paid) {
		return response('{"success": "false", "reason": "already under loan"}', 200);
	    }
			    
	    $data = $request->post('data');

	    $loan = new Loan();
	    // $loan->rate = (double)str_replace(',', '.', $data['tauxEffectifMensuelle']);
	    // $loan->amount = (double)$request->post('totalCapital');
	    $loan->capital = (double)$request->post('totalCapital');
	    $loan->interest = (double)$request->post('totalInterest');
	    $loan->amount = ((double)$request->post('totalCapital') + 
		    	     (double)$request->post('totalInterest'));
	    $loan->employee_id = $employee_id;
	    $loan->date = $request->post('loanDate');
	    if ($commentary) $loan->commentary = $commentary;
	    $loan->paid = false;
	    $loan->cancelled = false;

	    if ($loan->save()) {
		foreach ($data['echeances'] as $key => $value) {
		    $repayment = new Repayment();
		    $repayment->amount = (double) $value['capital'] + (double) $value['interets'];
		    $repayment->capital = $value['capital'];
		    $repayment->interest = $value['interets'];
		    $date_str = str_replace('/', '-', $value['date']);
		    $repayment->repayment_date = date("Y-m-d", strtotime($date_str));
		    $loan->repayments()->save($repayment);
		}
		return response('{"success": true}', 200);
	    }
	    return response('{"success": false, "reason": "Error"}', 200);
	}
	return response('{"success": false, "reason": "Employee not found"}', 200);
	/*if ($loan->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);*/
    }

    public function deleteLoan(int $id, Request $request)
    {
	$loan = Loan::find($id);
	if ($loan !== null) {
	    $loan->cancelled = true;
	    $loan->cancellation_date = $request->post('date');
	    $loan->cancellation_reason = $request->post('reason');
	    if ($loan->save()) return response('{"success": true}', 200);
	    else return response('{"success": false}', 200);
	}
	return response('{"success": false}', 200);
    }

    public function loanPaid(int $id)
    {
	$loan = Loan::find($id);
	$loan->paid = true;
	if ($loan->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }

    public function actualLoanList()
    {
	return Loan::where([
	    ['paid', false],
	    ['cancelled', false],
	])->with('employee')
	->with('repayments')
	->get();
    }

    public function actualLoanCount()
    {
	return Loan::where([
	    ['paid', false],
	    ['cancelled', false],
	])->count();
    }
}

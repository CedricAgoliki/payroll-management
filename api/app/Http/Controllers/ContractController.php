<?php

namespace App\Http\Controllers;

use App\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function endContract(Request $request)
    {
	$contract = Contract::find($request->post('id'));
	if ($contract !== null) {
	    $contract->ended = true;
	    $contact->endingDate = $request->post('date');
	    $contract->endindReason = $request->post('reason');
	    return response('{"success": true}', 200);
	}
	return response('{"success": false}', 200);
    }


    public function employeesAtEndOfContract()
    {
	$actualYear = (int) date('Y');
	$lastMonth = (int) date('m', strtotime('+2 months'));
	$actualMonth = (int) date('m');
	$contracts = Contract::with('employee')
			    ->whereYear('end', $actualYear)
			    ->whereMonth('end', '<=', $lastMonth)
			    ->whereMonth('end', '>=', $actualMonth)
			    ->get();
	return $contracts;
    }
}

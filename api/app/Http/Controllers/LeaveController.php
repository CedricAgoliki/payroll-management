<?php

namespace App\Http\Controllers;

use App\Leave;
use App\Employee;
use App\Contract;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function newLeave(Request $request)
    {
	$employee = Employee::find($request->post('employee'));
	if ($employee !== null) {
	    // the person bein assigned a leave should not
	    // be enjoying another leave TODO
	    $oldLeave = Leave::where('employee_id', $employee->id)
	    	    ->where('cancelled', false)
		    ->orderBy('end','desc')->first();
	    if ($oldLeave === null) {
		$leave = new Leave();
		$leave->leave_type_id = $request->post('type');
		$leave->start = $request->post('start');
		$leave->end = $request->post('end');
		$leave->cancelled = false;
		$leave->nb_days = $request->post('nbDays');
		$leave->reason = $request->post('reason');
		$leave->commentary = $request->post('commentary');
		$employee->leaves()->save($leave);
		return response('{"success": true}', 200);
	    }
	    return response('{"success": false, "message": "Employé deja en congé"}', 200);
	}
	return response('{"success": false, "message": "employee non trouvé"}', 200);
    }

    public function currentLeaves()
    {
	return Leave::whereDate('start', '<=', date('Y-m-d'))
	    	    ->whereDate('end', '>=', date('Y-m-d'))
	    	    ->where('cancelled', false)
		    ->with('employee')->get();
    }

    public function futureLeaves()
    {
	return Leave::whereDate('start', '>', date('Y-m-d'))
	    	    ->where('cancelled', false)
		    ->with('employee')->get();
    }

    public function cancelLeave(int $id, Request $request)
    {
	$leave = Leave::find($id);
	if ($leave !== null) {
	    $leave->cancelled = true;
	    $leave->cancellation_date = $request->post('date');
	    $leave->cancellation_reason = $request->post('reason');
	    if ($leave->save()) return response('{"success": true}', 200);
	    else return response('{"success": false}', 200);
	}
	return response('{"success": false}', 200);
    }

    public function count()
    {
	return Leave::whereDate('start', '<=', date('Y-m-d'))
		    ->whereDate('end', '>=', date('Y-m-d'))
		    ->with('employee')->count();
	// return Leave::count();
    }

    public function modifyLeave($id, Request $request)
    {
	$leave = Leave::find($id);
	if ($leave !== null) {
	    $leave->start = $request->post('start');
	    $leave->end = $request->post('end');
	    if ($leave->save()) return response('{"success": true}', 200);
	    return response('{"success": false, "message": "Une erreur s\'est produite"}', 200);
	}
	return response('{"success": false, "message": "Congé non trouvé"}', 200);
    }


    public function remainingLeavesPerEmployee()
    {
	$employees = Employee::all();
	$results = array();
	foreach ($employees as $employee) {
	    $leaves = Leave::where('employee_id', $employee->id)
		->where(function ($query) {
		    $query->whereYear('start', date('Y'))
			  ->orWhereYear('end', date('Y'));
		})->get();

	    $nbLeaveDays = 0;
	    foreach ($leaves as $leave) {
		/* $end = $leave->end;
		if ($leave->cancelled) $end = $leave->cancel_date;
		$nbLeaveDays += $this->nbBusinessDaysBetween($leave->start, $end);
		 */
		$nbLeaveDays += $leave->nb_days;
	    }
	    //$remaining = $employee->leaves_per_month * 12 - $nbLeaveDays;
	    $lastContract = Contract::where('employee_id', $employee->id)
				  ->orderBy('start', 'desc')->first();
	    $contractStart = new Carbon($lastContract->start);
	    $remaining = $employee->leaves_per_month * $contractStart->diffInMonths(Carbon::now()) - $nbLeaveDays;

	    $result = array(
		"employee" => $employee,
		"remainingLeaves" => $remaining,
		"takenLeaves" => $nbLeaveDays,
	    );
	    $results[] = $result;
	}
	return $results;
    }

}

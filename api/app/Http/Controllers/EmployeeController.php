<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Contract;
use App\Property;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /*public function __construct()
    {
	$this->middleware('auth:api');
    }*/


    public function allEmployeeWithSalary(string $startDate, string $endDate)
    {
	$employees = Employee::with([
	    'salaries' => function ($query) use ($startDate, $endDate) {
		    $query->whereDate('period_start',$startDate)
			    ->whereDate('period_end', $endDate)
			    ->with('settings')
			    ->with('elements');
	    },
	    'advances' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('date','>=',$startDate)
		    ->whereDate('date', '<=',$endDate);
	    },
	    'loans' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('date','>=',$startDate)
		    ->whereDate('date', '<=',$endDate);
	    }
	])->get();
	$valid = Array();
	foreach ($employees as $emp) {
	    if ($emp->salaries !== null && count($emp->salaries) > 0) {
		$valid[] = $emp;
	    }
	}
	return $valid;
    }

    /*public function allEmployeeWithSalary(int $month, int $year)
    {
	$employees = Employee::with([
	    'salaries' => function ($query) use ($month, $year) {
		// $query->where('date', $year.'/'.$month.'/1')->with('elements');	    
		    $query->whereMonth('date',$month)
			    ->whereYear('date', $year)
			    ->with('settings')
			    ->with('elements');
	    },
	    'advances' => function ($query) use ($month, $year) {
		$query->whereMonth('date', $month)->whereYear('date', $year);
	    },
	    'loans' => function ($query) use ($month, $year) {
		$query->whereMonth('date',$month)
		    ->whereYear('date', $year)
		    ->get();
	    }
	])->get();
	$valid = Array();
	foreach ($employees as $emp) {
	    if ($emp->salaries !== null && count($emp->salaries) > 0) {
		$valid[] = $emp;
	    }
	}
	return $valid;
    }*/

    public function allEmployee() {
	return Employee::all();
    }

    public function allEmployeeWithoutGroup() {
	return Employee::where('group_id', null)->get();
    }

    public function allEmployeeWithAttributes() {
	return Employee::with([
	    'contracts' => function ($query) {
		$query->orderby('created_at', 'desc')->first();
	    }])->with('properties')->get();
    }

    public function allEmployeeUnderContract() {
	// TODO
    }

    public function allEmployeeNotUnderContract() {
	// TODO
    }

    public function allEmployeeWithAttributesPaginated(int $n) {
	return Employee::with([
	    'contracts' => function ($query) {
		$query->orderby('created_at', 'desc')->first();
	    }])->with('properties')->paginate($n);
    }

    public function count() {
	return Employee::count();
    }

    public function getEmployee($id) {
	$employee = Employee::find($id);
	if ($employee !== null) return $employee;
	return response('{"success": true, "reason": "employee does not exist" }', 200);
    }

    public function updateEmployee($id, Request $request) {
	$employee = Employee::find($id);
	$data = json_decode($request->post('employee'), true);
	$contract = Contract::find($data['contractId']);
	if ($employee !== null && $contract !== null) {
	    $employee->first_name = $data['firstName'];
	    $employee->last_name = $data['lastName'];
	    // $employee->birthdate = $request->post('birthdate');
	    $employee->base_salary = $data['baseSalary'];
	    // $employee->gender = $request->post('gender');
	    $employee->leaves_per_month = $data['leavesPerMonth'];
	    $employee->dependants = $data['dependants'];

	    if ($data['registrationNumber'])
		$employee->registration_number = $data['registrationNumber'];
	    if ($data['socialSecurityNumber'])
		$employee->social_security_number = $data['socialSecurityNumber'];
	    if ($data['paymentMethod'])
		$employee->payment_method = $data['paymentMethod'];
	    if ($data['bankAccount'])
		$employee->bank_account = $data['bankAccount'];
	    if ($data['office'])
		$employee->office = $data['office'];
	    if ($data['category'])
		$employee->category = $data['category'];

	    // return $data;
	    if ($employee->save()) {
		foreach ($data['properties'] as $key => $value) {
		    $prop = Property::find($key);
		    // I think this is inefficiant, but for i works so...
		    $employee->properties()->detach($prop); 
		    $employee->properties()->attach($prop, ['value' => $value]);
		}
		$contract->start = $data['contractStart'];
		$contract->end = $data['contractEnd'];
		$contract->commentary = $data['contractCommentary'];
		$contract->save();
		// $employee->contracts()->save($contract);
		return response('{"success": true}', 200);
	    }
	    return response('{"success": false}', 200);
	}
	return response('{"success": false, "message": "Employé n\'existe pas"}', 200);
    }

    public function newEmployeeEntry(Request $request)
    {
	// return $request->post('employee');
	$data = json_decode($request->post('employee'), true);
	// return json_encode($data);
	// each new employee needs a contract
	if ($data !== null) {
	    $contract = new Contract();
	    $contract->start = $data['contractStart'];
	    $contract->end = $data['contractEnd'];
	    if ($data['contractCommentary'] !== null) {
		$contract->commentary = $data['contractCommentary'];
	    }
	    $contract->ended = false;
	    
	    // we then save the employee
	    $employee = new Employee();
	    $employee->first_name = $data['firstName'];
	    $employee->last_name = $data['lastName'];
	    // $employee->birthdate = $request->post('birthdate');
	    $employee->base_salary = $data['baseSalary'];
	    // $employee->gender = $request->post('gender');
	    $employee->leaves_per_month = $data['leavesPerMonth'];
	    $employee->dependants = $data['dependants'];

	    if ($data['registrationNumber'])
		$employee->registration_number = $data['registrationNumber'];
	    if ($data['socialSecurityNumber'])
		$employee->social_security_number = $data['socialSecurityNumber'];
	    if ($data['paymentMethod'])
		$employee->payment_method = $data['paymentMethod'];
	    if ($data['bankAccount'])
		$employee->bank_account = $data['bankAccount'];
	    if ($data['office'])
		$employee->office = $data['office'];
	    if ($data['category'])
		$employee->category = $data['category'];

	    if ($employee->save()) {
		foreach ($data['properties'] as $key => $value) {
		    $prop = Property::find($key);
		    // return $key;
		    $employee->properties()->attach($prop, ['value' => $value]);
		}
		$employee->contracts()->save($contract);
		return response('{"success": true}', 200);
	    }

	    return response('{"success": false}', 200);
	}
	return response('{"success": false, "reason": "invalid request"}', 200);
    }

    public function oldEmployeeEntry(Request $request)
    {
	// each new employee needs a contract
	$contract = new Contract();
	$contract->start = $request->post('contractStart');
	$contract->end = $request->post('contractEnd');
	$contract->commentary = $request->post('contractCommentary');
	$contract->ended = false;

	$id = $request->post('employeeId');
	$employee = Employee::find($id);
	//return $employee;
	if ($employee !== null) {
	    // TODO: we need to first check the employee
	    //have no contract
	    $lastContract = Contract::where('employee_id',$employee->id)
				      ->orderBy('start','desc')->first();
	    if ($lastContract !== null && $contract->start > $lastContract->end) {
		$employee->contracts()->save($contract);
		return response('{"success": true}', 200);
	    }

	    return response('{"success": false, "reason": "employee is already under contract"}', 200);
	}

	return response('{"success": false, "reason": "employee does not exist"}', 200);

    }

    public function employeeExit(Request $request)
    {
	$employeeId = $request->post('employeeId');
	$employee = Employee::find($request->post('employeeId'));
	if ($employee !== null) {
	    $lastContract = Contract::where('employee_id',$employee-id)
					  ->orderBy('start','desc')->first();
	    if ($lastContract !== null) {
		$lastContract->commentary = $request->post('contractCommentary');
		$lastContract->ended = true;
		$lastContract->save();
		return response('{"success": true}', 200);
	    }
	    return response('{"success": false, "reason": "employee not under contract"}', 200);
	}

	return response('{"success": false, "reason": "employee does not exist"}', 200);
    }


    public function renewContract(int $id, Request $request)
    {
	$employee = Employee::find($id);
	if ($employee !== null) {

	    /*$contract = new Contract();
	    $contract->start = $request->post('contractStart');
	    $contract->end = $request->post('contractEnd');
	    $contract->commentary = $request->post('contractCommentary');
	    $contract->employee_id = $employee->id;*/
	    $contract = Contract::where('employee_id', $id)->orderby('start', 'desc')->first();
	    $contract->end = $request->post('contractEnd');
	    $contract->commentary = $request->post('contractCommentary');
	    if ($contract->save()) {
		return response('{"success": true}', 200);
	    }
	    return response('{"success": false, "reason": "An error occured"}', 200);
	}

	return response('{"success": false, "reason": "employee not found"}', 200);
    }

    public function employeeDelete()
    {
	// TODO:delete the employee and all his data
    }

}

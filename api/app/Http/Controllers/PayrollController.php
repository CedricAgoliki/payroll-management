<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Employee;
use App\Settings;
use App\Salary;
use App\Leave;
use App\Element;
use App\Contract;
use App\Repayment;
use Illuminate\Http\Request;
use PDF;

class PayrollController extends Controller
{
    public function recompute(Request $request)
    {
	$startDate = $request->post('start');
	$endDate = $request->post('end');
	$holidays = $request->post('holidays');
	$salaries = Salary::whereDate('period_start', $startDate)
			    ->whereDate('period_end', $endDate)
			    ->get(); 
	foreach ($salaries as $salary) {
	    $salary->elements()->detach();
	    $salary->delete();
	}
	$this->generate($startDate, $endDate, $holidays);
	return response('{"success" : true}', 200);
    }

    public function get(Request $request)
    {
	$startDate = $request->post('start');
	$endDate = $request->post('end');
	$holidays = $request->post('holidays');
	$salaries = Salary::whereDate('period_start', $startDate)
			    ->whereDate('period_end', $endDate)
			    ->with('elements')
			    ->with('settings')
			    ->get(); 
	if (sizeof($salaries) === 0) {
	    $this->generate($startDate, $endDate, $holidays);
	    $salaries = Salary::whereDate('period_start', $startDate)
			    ->whereDate('period_end', $endDate)->get();
	}
	return $salaries;
    }

    public function generate(string $startDate, string $endDate, array $holidays)
    {
	$employees = Employee::all();
	$setting = Settings::orderBy('created_at','desc')->first();
	if ($setting === null) {
	    return response('{"success" : false, "reason": "undefined settings"}', 200);
	}
	foreach ($employees as $emp) {
	    if ($emp->group === null) continue; //skip employees not in any group
	    $salary = new Salary();
	    // a backup of the settings at this specific moment
	    // is made just in case settings or base_salary changes
	    $salary->employee_id = $emp->id;
	    //$salary->date = $year . '-' . $month . '-01';
	    //$salary->date = $setting->payday;
	    $salary->date = new Carbon($endDate);
	    $salary->period_start = new Carbon($startDate);
	    $salary->period_end = new Carbon($endDate);
	    $salary->holidays = json_encode($holidays);
	    $salary->base_salary = $emp->base_salary;

	    $salary->leave_balance = $emp->leaves_per_month;
	    $salary->seniority_bonus = $emp->group->seniority_bonus;

	    $salary->bank_account = $emp->bank_account;
	    $salary->registration_number = $emp->registration_number;
	    $salary->office = $emp->office;
	    $salary->category = $emp->category;
	    $salary->social_security_number = $emp->social_security_number;
	    $salary->payment_method = $emp->payment_method;

	    $salary->reviewed = false;
	    $salary->settings_id = $setting->id;

	    $res = $this->numberOfWorkedDays($emp, $startDate, $endDate, $holidays);
	    // $salary->openDays = $openDays;
	    $salary->workedDays = $res["workedDays"];
	    $salary->openDays = $res["openDays"];

	    // pay all loans in this period
	    foreach ($emp->loans as $loan) {
		    $repayments = Repayment::where('loan_id', $loan->id)
			    ->where('repayment_date', '>=',$startDate)
			    ->where('repayment_date', '<=', $endDate)->get();
		    foreach ($repayments as $repayment) {
			    $repayment->paid = true;
			    $repayment->save();
		    }
	    }

	    if ($salary->save()) {
		$this->setSalaryElements($salary, $emp);
	    }
	}
    }


    public function numberOfWorkedDays(Employee $employee, string $startDate, 
					string $endDate, array $holidays) {
	$month = array();
	$start = new Carbon($startDate);
	$end = new Carbon($endDate);

	$lastContract = Contract::where('employee_id', $employee->id)
				  ->orderBy('start', 'desc')->first();
	if ($start->lessThan($lastContract->start))
	  $start = new Carbon($lastContract->start);

	if ($end->greaterThan($lastContract->end))
	  $end = new Carbon($lastContract->end);

	// create the sieve and remove weekends
	while ($start->lessThanOrEqualTo($end)) {
	    if ($start->isWeekend())
		$month[$start->toDateString()] = 0;
	    else 
		$month[$start->toDateString()] = 1;
	    $start->addDay(1);
	}

	// remove holidays
	foreach ($holidays as $day) {
	    if (array_key_exists($day, $month)) {
		$month[$day] = 0;
	    }
	}


	//count the open days
	$nbOpenDays = 0;
	foreach ($month as $day) {
	    if ($day == 1) $nbOpenDays++;
	}

	
	//remove leaves
	$leaves = Leave::where('employee_id', $employee->id)
			  ->where(function ($query) use ($startDate, $endDate) {
			      $query->where('start', '>=', $startDate)
				    ->where('start', '<=', $endDate);
			    })
			    ->orWhere(function ($query) use ($startDate, $endDate) {
				$query->where('end', '>=', $startDate)
				    ->where('end', '<=', $endDate);
			    })->get();
	foreach ($leaves as $leave) {
	    $start = $leave->start;
	    $end = $leave->end;
	    while ($start->notEqualTo($end)) {
		if ($start->greaterThanOrEqualTo($startDate) 
		    && $start->lessThanOrEqualTo($endDate)) {
		    $month[$start->toDateString()] = 0;
		}
		$start->addDay(1);
	    }
	}


	//count the remaining
	$nbWorkedDays = 0;
	foreach ($month as $day) {
	    if ($day == 1) $nbWorkedDays++;
	}

	return array( 
	    "workedDays" => $nbWorkedDays,
	    "openDays" => $nbOpenDays
	);
	
    }

    /*public function numberOfWorkedDays(Employee $employee, int $month, int $year, array $holydays)
    {
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$payday = $setting->payday;

	// intervall of worked days
	$endDate = Carbon::createFromDate($year, $month, $payday);
	$lastPayDate = $endDateClone->copy()->subMonth(1);
	$contract = Contract::where('employee_id', $employee->id)
			    ->where(function ($query) {
				$query->where('start', '>=', $lastPayDate)
				    ->where('start', '<=', $endDate);
			    })
			    ->orWhere(function ($query) {
				$query->where('end', '>=', $lastPayDate)
				    ->where('end', '<=', $endDate);
			    })
			    ->orderBy('end', 'desc')
			    ->first();
	$lastContract = $contract->start;

	if ($lastContract->greaterThan($lastPayDate)) {
	    $startDate = $lastContract->start;
	} else {
	    $lastContract = $lastPayDate;
	}
	$nbOfDays = $startDate->diffInDays($endDate);
	$month = array();
	$end = clone $endDate;
	while ($start->notEqualTo($endDate)) {
	    $month[$start->toDateString()] = 1;
	    $start->addDay(1);
	}

	// remove leaves
	foreach ($leaves as $leave) {
	    $start = $leave->start;
	    $end = $leave->end;
	    while ($start->notEqualTo($end)) {
		if ($start->greaterThanOrEqualTo($startDate) 
		    && $start->lessThanOrEqualTo($endDate)) {
		    $month[$start->toDateString()] = 0;
		}
		$start->addDay(1);
	    }
	}

	//count remaining days
	//$month = array_fill(0, $nbOfDays, 1);
	$workedDay = 0;
	foreach ($month as $key => $value) {
	    if ($value === 1) $workedDay++;
	}

	return $workedDay;
    }*/


    /*public function setSalarySettings(Salary &$salary, Settings $settings)
    {
	$salary->sum_per_dependants = $settings->sum_per_dependants;
	$salary->percentage_personal_fees = $settings->percentage_personal_fees;
	$salary->threshold = $settings->threshold;
	$salary->percentage_after_threshold = $settings->percentage_after_threshold;
	$salary->payroll_tax = $settings->payroll_tax;
	$salary->employer_contribution_rate = $settings->employer_contribution_rate;
	$salary->employee_contribution_rate = $settings->employee_contribution_rate;
	$salary->tax_schedule = $settings->tax_schedule;
    }*/

    public function setSalaryElements(Salary &$salary, Employee $employee)
    {
	$elements = $employee->group->elements;
	foreach ($elements as $element) {
	    $value = $element->pivot->value;
	    $allowed = $element->pivot->allowed;
	    $variable = $element->pivot->variable;
	    $salary->elements()->attach($element, [
		'value' => $value,
		'allowed' => $allowed,
		'variable' => $variable
	    ]);
	}
    }

    public function saveSalary(Request $request)
    {
	$elements = $request->post('elements');
	$salary = Salary::find('id');
	foreach ($elements as $element) {
	    $el = Element::find($element->id);
	    $el->attach($salary, ['value' => $element->value]);
	}
	return response('{"success": true}', 200);
    }

    public function validateSalary(int $id)
    {
	$salary = Salary::find($id);
	if ($salary !== null) {
	    $salary->reviewed = true;
	    if ($salary->save()) {
		return response('{"success": true}', 200);
	    }
	}
	return response('{"success": false, "message": "An error occured"}', 200);
    }

    public function modifySalary(int $id, Request $request)
    {
	$data = json_decode($request->post('data'), true);
	$salary = Salary::find($id);
	if ($salary !== null) {

	    $salary->elements()->detach();

	    foreach ($data['primes'] as $key => $value) {
		$element = Element::find($key);
		if ($element !== null) {
		    $element->salaries()->attach($salary, [
			'value' => $value,
			'allowed' => $data['allowed'][$key],
			'variable' => $data['variable'][$key]
		    ]);
		}
	    }

	    foreach ($data['deductions'] as $key => $value) {
		$element = Element::find($key);
		if ($element !== null) {
		    $element->salaries()->attach($salary, [
			'value' => $value,
			'allowed' => $data['allowed'][$key],
			'variable' => $data['variable'][$key]
		    ]);
		}
	    }

	    $salary->reviewed = true;
	    $salary->modification_reason = $data['reason'];
	    if ($salary->save()) {
		return Salary::with('elements')->with('settings')->where('id', $id)->get();
	    }
	    return response('{"success": false, "message": "An error occured"}', 200);
	}
	return response('{"success": false, "message": "Salary not found"}', 200);
    }



    public function printSalaryWeb(int $id, string $startDate, string $endDate)
    {
	// $startDate = $request->post('start');
	// $endDate = $request->post('end');
	$primes = app(ElementController::class)->primes();
	$deductions = app(ElementController::class)->deductions();
	$employee = Employee::with([
	    'salaries' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('period_start', $startDate)->whereDate('period_end', $endDate);
	    },
	    'contracts' => function ($query) {
		$query->orderBy('end', 'desc')->first();
	    },
	    'advances' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('date','>=' ,$startDate)->whereDate('date','<=' ,$endDate);
	    },
	    'loans' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('date', '>=',$startDate)
		    ->whereDate('date', '<=',$endDate)
		    ->get();
	    }
	])->find($id);
	$setting = Settings::orderBy('created_at','desc')->first();
	return view('pdf.single', ['emp' => $employee,
				    'primes' => $primes,
				    'deductions' => $deductions,
				    'setting' => $setting,
				    'startDate' => $startDate, 
				    'endDate' => $endDate
				]);
	/*$pdf = PDF::loadView('pdf.single', ['emp' => $employee,
					'primes' => $primes,
					'deductions' => $deductions])
					->setPaper('A4','portrait');
	$name = 'fiche_' . $employee->last_name 
		. '_' . $employee->first_name 
		. '_' . $startDate . '_' . $endDate . '.pdf';
	return $pdf->download($name);*/
    }



    public function printSalary(int $id, string $startDate, string $endDate)
    {
	// $startDate = $request->post('start');
	// $endDate = $request->post('end');
	$primes = app(ElementController::class)->primes();
	$deductions = app(ElementController::class)->deductions();
	$employee = Employee::with([
	    'salaries' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('period_start', $startDate)->whereDate('period_end', $endDate);
	    },
	    'contracts' => function ($query) {
		$query->orderBy('end', 'desc')->first();
	    },
	    'advances' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('date','>=' ,$startDate)->whereDate('date','<=' ,$endDate);
	    },
	    'loans' => function ($query) use ($startDate, $endDate) {
		$query->whereDate('date', '>=',$startDate)
		    ->whereDate('date', '<=',$endDate)
		    ->get();
	    }
	])->find($id);
	$setting = Settings::orderBy('created_at','desc')->first();
	/*return view('pdf.single', ['emp' => $employee,
				    'primes' => $primes,
				    'deductions' => $deductions,
				    'setting' => $setting,
				    'startDate' => $startDate, 
				    'endDate' => $endDate
			    ]);*/
	$pdf = PDF::loadView('pdf.single', ['emp' => $employee,
					'primes' => $primes,
					'deductions' => $deductions,
				    	'setting' => $setting,
				    	'startDate' => $startDate, 
				    	'endDate' => $endDate])
					->setPaper('A4','portrait');
	$name = 'fiche_' . $employee->last_name 
		. '_' . $employee->first_name 
		. '_' . $startDate . '_' . $endDate . '.pdf';
	return $pdf->download($name);
    }

    public function printAllSalary(string $startDate, string $endDate)
    {
	// $startDate = $request->post('start');
	// $endDate = $request->post('end');

	$primes = app(ElementController::class)->primes();
	$deductions = app(ElementController::class)->deductions();
	$emps = app(EmployeeController::class)->allEmployeeWithSalary($startDate, $endDate);
	$validEmployees = Array();
	foreach ($emps as $emp) {
	    if ($emp->salaries !== null && count($emp->salaries) > 0) {
	      $validEmployees[] = $emp;
	    }
	}
	/*return view('pdf.all', ['employees' => $validEmployees,
					'primes' => $primes,
					'deductions' => $deductions]);*/
	$setting = Settings::orderBy('created_at','desc')->first();
	$pdf = PDF::loadView('pdf.all', ['employees' => $emps,
					'primes' => $primes,
					'deductions' => $deductions,
				    	'setting' => $setting,
				    	'startDate' => $startDate, 
				    	'endDate' => $endDate])
					->setPaper('A4','portrait');
	return $pdf->download('fiches_'. $startDate . '_' . $endDate . '.pdf');
    }

}

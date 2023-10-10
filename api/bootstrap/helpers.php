<?php
use Carbon\Carbon;
use App\Employee;
use App\Advance;
use App\Settings;

function test() {
    return "just testing";
}

function leaveBalance($employee) {
    $leaves = $employee->leaves()->where('employee_id', $employee->id)
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
	    $lastContract = $employee->contracts()->where('employee_id', $employee->id)
				  ->orderBy('start', 'desc')->first();
	    $contractStart = new Carbon($lastContract->start);
	    $remaining = $employee->leaves_per_month * $contractStart->diffInMonths(Carbon::now()) - $nbLeaveDays;
    return $remaining;
}

function totalPrimes(Employee $employee) {
    $total = 0;
    $elements = $employee->salaries[0]->elements;
    foreach ($elements as $e) {
	if ($e->type === '+' && $e->pivot->allowed) {
	    $total += $e->pivot->value;
	}
    }
    return $total;
}

function totalDeductions(Employee $employee, string $startDate, string $endDate) {
    if ($employee != null) {
	$total = 0;
	$elements = $employee->salaries[0]->elements;
	foreach ($elements as $e) {
	    if ($e->type === '-' && $e->pivot->allowed) {
		$total += $e->pivot->value;
	    }
	}
	$total += advances($employee->id, $startDate, $endDate);
	$total += loans($employee->id, $startDate, $endDate);
	return $total;
    }
    return -1;
}

function advances(int $id, string $startDate, string $endDate) {
    //$date = $employee->salaries[0]->date;
    /*$month = (int) date('m', strtotime($date));
    $month = (int) date('Y', strtotime($date));*/
    $toPay = 0;
    // $advances = $employee->advances->where('date');
    $advances = Advance::where('employee_id', $id)
			->where('payment_date', '>=', $startDate)
			->where('payment_date', '<=', $endDate)->get();
    foreach ($advances as $advance) {
	$toPay += $advance->amount;
    }
    return $toPay;
}

function loans(int $id, string $startDate, string $endDate) {
    $employee = Employee::find($id);
    $toPay = 0;
    if ($employee != null) {
	$loans = $employee->loans;
	foreach ($loans as $loan) {
	    $repayments = $loan->repayments->where('repayment_date', '>=', $startDate)
					    ->where('repayment_date', '<=', $endDate);
	    foreach ($repayments as $repayment) {
		$toPay += $repayment->amount;
	    }
	}
    }
    /*var_dump($loans);
    if (sizeof($employee->loans) > 0) {

	// $repayments = $employee->loans();
	//return $employee->loans[0]->amount;
	return -1;
    }*/
    return $toPay;
}

function baseSalary($employee) {
    return $employee->salaries[0]->base_salary;
}

function mandatoryDeductions(Employee $employee) {
    $total = 0;
    //$total += advances($id, $startDate, $endDate);
    //total += loans($id, $startDate, $endDate);
    //var_dump($employee);
    $total += irpp($employee);
    $total +=cnss($employee);
    $total += tcs($employee);
    return $total;
}

function otherDeductions(Employee $employee, string $startDate, string $endDate) {
    $total = totalDeductions($employee, $startDate, $endDate);
    $total -= advances($employee->id, $startDate, $endDate);
    $total -= loans($employee->id, $startDate, $endDate);
    return $total;
}

function netSalary($employee) {
    return grossSalary($employee) - mandatoryDeductions($employee);
}

function grossSalary(Employee $employee) {
    return baseSalary($employee) + totalPrimes($employee);
}

function netToPay(Employee $employee, string $startDate, string $endDate) {
    return netSalary($employee) - otherDeductions($employee, $startDate, $endDate);
}

function monthAndYear($employee) {
    setlocale(LC_TIME, "fr");
    $date = $employee->salaries[0]->date;
    $formatted_date = strftime("%B %G",strtotime($date));
    return $formatted_date;
}

function tcs(Employee $employee) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $tcs = $setting->payroll_tax;
    if ($tcs != null) {
	$tcs = json_decode($tcs, true);
	$irpp = irpp($employee); 
	if ($irpp != -1) {
	    if ($irpp > 0) return (int) $tcs['irpp'];
	    else return (int) $tcs['noirpp'];
	}
    }
    return -1;
}


function totalPrimesIrpp($employee) {
    $total = 0;
    $elements = $employee->salaries[0]->elements;
    foreach ($elements as $e) {
	if ($e->type === '+' && $e->pivot->allowed && $e->irpp) {
	    $total += $e->pivot->value;
	}
    }
    return $total;
}

function irpp(Employee $employee) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $irpp = $setting->personnal_income_tax;
    if ($irpp != null) {
	$irpp = json_decode($irpp, true);
	$wage = (int) taxableWages($employee);
	foreach($irpp as $interval) {
	    if ($wage >= (int) $interval['from'] && 
		$wage <= (int) $interval['to']) {
		    return (int) $interval['rate'] * 0.01 * $wage;
	    }
	}
    }
    return -1;
}


function irppRate($employee) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $irpp = $setting->personnal_income_tax;
    if ($irpp != null) {
	$irpp = json_decode($irpp, true);
	$wage = (int) taxableWages($employee);
	foreach($irpp as $interval) {
	    if ($wage >= (int) $interval['from'] && 
		$wage <= (int) $interval['to']) {
		    return (int) $interval['rate'];
	    }
	}
    }
    return -1;
}

function seniority_bonus($employee, $endDate) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $seniority = $setting->seniority_bonus;
    if ($seniority != null) {
	$seniority = json_decode($seniority, true);
	$salary = (int) $employee->base_salary;

    	$lastContract = $employee->contracts()->where('employee_id', $employee->id)
				  ->orderBy('start', 'desc')->first();
    	$start = new Carbon($lastContract->start);
    	$end = new Carbon($endDate);
    	$years = $start->diffInYears($end);
	foreach  ($seniority as $interval) {
	    if ($years >= (int) $interval['from'] && 
		$years <= (int) $interval['to']) {
		if ($interval['type'] == 'fixed') {
		    return $salary * ((int) $interval['rate']) / 100;
		} else if ($interval['type'] == 'incremental') {
		    return $salary * ((int) $interval['rate'] * $years) / 100;
		}
		return 0;
	    }
	}
    }
    return 0;
}

function seniority_bonusRate($employee, $endDate) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $seniority = $setting->seniority_bonus;
    if ($seniority != null) {
	$seniority = json_decode($seniority, true);

    	$lastContract = $employee->contracts()->where('employee_id', $employee->id)
				  ->orderBy('start', 'desc')->first();
    	$start = new Carbon($lastContract->start);
    	$end = new Carbon($endDate);
    	$years = $start->diffInYears($end);
	foreach  ($seniority as $interval) {
	    if ($years >= (int) $interval['from'] && 
		$years <= (int) $interval['to']) {
		if ($interval['type'] == 'fixed') {
		    return ((int) $interval['rate']) / 100;
		} else if ($interval['type'] == 'incremental') {
		    return ((int) $interval['rate'] * $years) / 100;
		}
		return 0;
	    }
	}
    }
    return 0;
}

function cnss($employee) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $rate = $setting->employee_contribution_rate;
    return (totalPrimesIrpp($employee) * $rate) / 100;
}

function grossSalaryAfterCnss(Employee $employee) {
    return grossSalary($employee) - cnss($employee); 
}

function abatement1($employee) {
    return grossSalaryAfterCnss($employee) * 0.28; // 28%
}

function grossSalaryAfterAbatement(Employee $employee) {
    return grossSalaryAfterCnss($employee) - abatement1($employee);
}

function taxableWages(Employee $employee) {
    $setting = Settings::orderBy('created_at','desc')->first();
    $dependants = $employee->salaries[0]->dependants;
    $perDependant = $setting->sum_per_dependants;
    return grossSalaryAfterAbatement($employee) - $dependants * $perDependant;
}

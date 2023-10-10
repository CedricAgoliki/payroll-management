<?php

namespace App\Http\Controllers;

use App\Settings;
use App\Salary;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function get()
    {
	$settings = Settings::orderBy('created_at','desc')->first();
	if ($settings === null) {
	    $settings = new Settings();
	    $settings->save();
	}
	// $settings = Settings::orderBy('created_at','desc')->first();
	return $settings;
    }

    public function modify(Request $request)
    {
	$settings = Settings::all()->first();
	if ($settings === null) {
	    // first use, initialize the settings
	    $settings = new Settings();
	    $settings->save();
	}
	$settings = Settings::all()->first();
	$settings->max_dependants = $request->post('maxDependants');
	$settings->sum_per_dependants = $request->post('sumPerDependants');
	$settings->percentage_personal_fees = $request->post('percentagePersonalFees');
	$settings->threshold = $request->post('threshold');
	$settings->percentage_after_threshold = $request->post('percentageAfterThreshold');
	$settings->tax_schedule = $request->post('taxSchedule');
	// put the values and save
	if ($settings->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }

    public function currentSettingsUsed()
    {
	// Checks if the current settings have been used
	// in the calculus of some salaries
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$count = Salary::where('settings_id', $setting->id)->count();
	if ($count > 0) return true;
	return false;
    }

    public function modifyPayrollTax(Request $request)
    {
	// stored directly as json
	$tcs = $request->post('tcs');
	// stored directly as json
	//$irpp = $request->post('data');
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$setting->payroll_tax = $tcs;
	if ($this->currentSettingsUsed()) {
	    $setting = $setting->replicate();
	}

	if ($setting->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }

    public function modifyPersonnalIncomeTax(Request $request)
    {
	// stored directly as json
	$irpp = $request->post('irpp');
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$setting->personnal_income_tax = $irpp;
	if ($this->currentSettingsUsed()) {
	    $setting = $setting->replicate();
	}

	if ($setting->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }

    public function modifySocialSecuritySettings(Request $request)
    {
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$setting->employer_contribution_rate = $request->post('employerRate');
	$setting->employee_contribution_rate = $request->post('employeeRate');
	if ($this->currentSettingsUsed()) {
	    $setting = $setting->replicate();
	}

	if ($setting->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }

    public function modifySeniorityBonus(Request $request)
    {
	// stored directly as json
	$sen = $request->post('sen');
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$setting->seniority_bonus = $sen;
	if ($this->currentSettingsUsed()) {
	    $setting = $setting->replicate();
	}

	if ($setting->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }

    //
    public function modifyOtherSettings(Request $request)
    {
	$setting = Settings::orderBy('created_at', 'desc')->first();
	$setting->director = $request->post('director');
	if ($this->currentSettingsUsed()) {
	    $setting = $setting->replicate();
	}

	if ($setting->save()) return response('{"success": true}', 200);
	return response('{"success": false}', 200);
    }
}

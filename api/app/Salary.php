<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;
use App\Element;
use App\Settings;

class Salary extends Model
{
    public function employee()
    {
	return $this->belongsTo(Employee::class);
    }

    public function elements()
    {
	return $this->belongsToMany(Element::class)
		    ->withPivot('value', 'allowed', 'variable');
    }

    public function settings()
    {
	return $this->belongsTo(Settings::class);
    }
}

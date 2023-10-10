<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Contract;
use App\Leave;
use App\Loan;
use App\Property;
use App\Salary;
use App\Group;

class Employee extends Model
{
    public function contracts()
    {
	return $this->hasMany(Contract::class);
    }

    public function leaves()
    {
	return $this->hasMany(Leave::class);
    }

    public function loans()
    {
	return $this->hasMany(Loan::class);
    }

    public function advances()
    {
	return $this->hasMany(Advance::class);
    }

    
    public function properties()
    {
	return $this->belongsToMany(Property::class)
	    ->withPivot('value');
    }

    public function salaries()
    {
	return $this->hasMany(Salary::class);
    }

    public function group()
    {
	return $this->belongsTo(Group::class);
    }
}

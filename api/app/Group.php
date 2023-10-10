<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;
use App\Element;

class Group extends Model
{
    //
    public function employees()
    {
	return $this->hasMany(Employee::class);
    }

    public function elements()
    {
	return $this->belongsToMany(Element::class)
		    ->withPivot('value','allowed', 'variable');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class Property extends Model
{ 
    public function properties()
    {
	return $this->belongsToMany(Employee::class);
    }
}

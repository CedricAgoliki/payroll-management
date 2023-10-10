<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class Advance extends Model
{
    public function employee()
    {
	return $this->belongsTo(Employee::class);
    }
}

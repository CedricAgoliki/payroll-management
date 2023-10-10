<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class Leave extends Model
{
    public function employee()
    {
	return $this->belongsTo(Employee::class);
    }
}

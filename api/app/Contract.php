<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class Contract extends Model
{
    public function employee()
    {
	return $this->belongsTo(Employee::class);
    }
}

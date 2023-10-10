<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;
use App\Repayment;

class Loan extends Model
{
    public function employee()
    {
	return $this->belongsTo(Employee::class);
    }

    public function repayments()
    {
	return $this->hasMany(Repayment::class);
    }
}

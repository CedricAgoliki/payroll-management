<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Loan;

class Repayment extends Model
{
    //
    public function loan()
    {
	return $this->belongsTo(Loan::class);
    }
}

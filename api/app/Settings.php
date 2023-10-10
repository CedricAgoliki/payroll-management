<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Salary;

class Settings extends Model
{
    //
    public function Salaries()
    {
	$this->has_many(Salary::class);
    }
}

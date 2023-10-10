<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Salary;
use App\Group;

class Element extends Model
{
    public function salaries()
    {
	return $this->belongsToMany(Salary::class);
    }

    public function groups()
    {
	return $this->belongsToMany(Group::class);
    }
}

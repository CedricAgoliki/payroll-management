<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Leave;

class LeaveType extends Model
{
    public function leaves()
    {
	return $this->hasMany(Leave::class);
    }
}

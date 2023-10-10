<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('employee_property', function (Blueprint $table) {
	    $table->string('value');
	});

	Schema::table('employee_property', function (Blueprint $table) {
	    $table->unsignedInteger('employee_id');
	    $table->unsignedInteger('property_id');
	    $table->foreign('employee_id')->references('id')->on('employees');
	    $table->foreign('property_id')->references('id')->on('properties');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::dropIfExists('employee_property');
    }
}

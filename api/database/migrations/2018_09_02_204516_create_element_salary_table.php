<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElementSalaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('element_salary', function (Blueprint $table) {
	    $table->string('value');
	    $table->boolean('allowed');
	    $table->boolean('variable');
	});

	Schema::table('element_salary', function (Blueprint $table) {
	    $table->unsignedInteger('element_id');
	    $table->unsignedInteger('salary_id');
	    $table->foreign('salary_id')->references('id')->on('salaries');
	    $table->foreign('element_id')->references('id')->on('elements');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('element_salary');
    }
}

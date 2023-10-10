<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
	    $table->date('start');
	    $table->date('end')->nullable();
	    $table->text('commentary')->nullable();
	    $table->boolean('ended');
            $table->timestamps();
        });

        Schema::table('contracts', function (Blueprint $table) {
	    $table->unsignedInteger('employee_id');
	    $table->foreign('employee_id')->references('id')->on('employees');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}

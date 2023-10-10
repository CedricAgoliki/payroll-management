<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
	    $table->date('start');
	    $table->date('end');
	    $table->text('reason')->nullable();
	    $table->boolean('cancelled');
	    $table->date('cancellation_date')->nullable();
	    $table->text('cancellation_reason')->nullable();
	    $table->tinyInteger('nb_days');
	    $table->text('commentary')->nullable();
            $table->timestamps();
        });

        Schema::table('leaves', function (Blueprint $table) {
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
        Schema::dropIfExists('leaves');
    }
}

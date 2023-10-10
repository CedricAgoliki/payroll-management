<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('rate')->nullable();
            $table->decimal('interest');
            $table->decimal('capital');
            $table->decimal('amount');
            $table->date('date');
            $table->text('commentary')->nullable();
            $table->boolean('paid')->nullable();
            $table->boolean('cancelled')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::table('loans', function (Blueprint $table) {
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
        Schema::dropIfExists('loans');
    }
}

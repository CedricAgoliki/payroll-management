<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advances', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount');
            $table->date('date');
            $table->date('payment_date');
            $table->text('commentary')->nullable();
            $table->string('type');
            $table->boolean('paid')->nullable();
            $table->boolean('cancelled')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::table('advances', function (Blueprint $table) {
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
        Schema::dropIfExists('advances');
    }
}

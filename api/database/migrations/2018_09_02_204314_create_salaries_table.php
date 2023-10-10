<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->date('period_start');
            $table->date('period_end');
            $table->text('holidays')->nullable();
            $table->decimal('base_salary')->nullable();
            $table->decimal('ratio')->nullable(); //ratio primes
            $table->boolean('reviewed');
            $table->text('modification_reason')->nullable();

            $table->decimal('leave_balance')->default("0");
            $table->boolean('seniority_bonus')->default("0");

	    $table->string('registration_number')->nullable();
	    $table->string('social_security_number')->nullable();
	    $table->string('payment_method')->nullable();
	    $table->string('bank_account')->nullable();
	    $table->string('office')->nullable();
	    $table->string('category')->nullable();
	    $table->tinyInteger('dependants')->default("0");

	    $table->string('openDays')->nullable();
	    $table->string('workedDays')->nullable();

	    // and many more
            $table->timestamps();
        });
        Schema::table('salaries', function (Blueprint $table) {
	    $table->unsignedInteger('employee_id');
	    $table->unsignedInteger('settings_id');
	    $table->foreign('employee_id')->references('id')->on('employees');
	    $table->foreign('settings_id')->references('id')->on('settings');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}

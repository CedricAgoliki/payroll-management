<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
	    $table->string('first_name');
	    $table->string('last_name');
	    $table->decimal('base_salary');
	    $table->date('birthdate')->nullable();
	    $table->char('gender', 1)->nullable();
	    $table->tinyInteger('leaves_per_month');
	    $table->tinyInteger('dependants')->default("0");
	    $table->string('registration_number')->nullable();
	    $table->string('social_security_number')->nullable();
	    $table->string('payment_method')->nullable();
	    $table->string('bank_account')->nullable();
	    $table->string('office')->nullable();
	    $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}

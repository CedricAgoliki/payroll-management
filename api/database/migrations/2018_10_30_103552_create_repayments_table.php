<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount');
            $table->date('repayment_date')->nullable();
            $table->decimal('capital');
            $table->decimal('interest');
            $table->boolean('cancelled')->nullable();
            $table->boolean('paid')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

	Schema::table('repayments', function (Blueprint $table) {
	    $table->unsignedInteger('loan_id');
	    $table->foreign('loan_id')->references('id')->on('loans');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repayments');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->increments('id');
	    $table->string('label');
	    $table->boolean('deduct');
	    $table->text('description')->nullable();
            $table->timestamps();
	});

	Schema::table('leaves', function (Blueprint $table) {
	    $table->unsignedInteger('leave_type_id');
	    $table->foreign('leave_type_id')->references('id')->on('leave_types');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_types');
    }
}

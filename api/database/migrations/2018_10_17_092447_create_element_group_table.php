<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElementGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
	Schema::create('element_group', function (Blueprint $table) {
	    $table->string('value');
	    $table->boolean('allowed');
	    $table->boolean('variable');
	});

	Schema::table('element_group', function (Blueprint $table) {
	    $table->unsignedInteger('element_id');
	    $table->unsignedInteger('group_id');
	    $table->foreign('group_id')->references('id')->on('groups');
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
        Schema::dropIfExists('element_group');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
	    $table->decimal('max_dependants')->nullable();
	    $table->decimal('sum_per_dependants')->nullable();
	    $table->decimal('percentage_personal_fees')->nullable();
	    $table->decimal('threshold')->nullable();
	    $table->decimal('percentage_after_threshold')->nullable();
	    $table->text('payroll_tax')->nullable(); // TCS
	    $table->text('personnal_income_tax')->nullable(); // IRPP
	    $table->text('seniority_bonus')->nullable(); // prime d'ancienneté
	    $table->decimal('employer_contribution_rate')->nullable();
	    $table->decimal('employee_contribution_rate')->nullable();
	    $table->decimal('tax_schedule')->nullable(); // bareme
	    $table->string('director')->nullable(); // nom du directeur
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
        Schema::dropIfExists('settings');
    }
}

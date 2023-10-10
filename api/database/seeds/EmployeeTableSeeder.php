<?php

use Illuminate\Database\Seeder;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	factory(App\Employee::class, 50)->create()->each(function ($e) {
	    $e->contracts()->save(factory(App\Contract::class)->make());
	});
    }
}

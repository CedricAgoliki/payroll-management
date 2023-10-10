<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});


// Route::resource('employee', 'EmployeeController');
// Employee routes
Route::group(['prefix' => 'employee'], function () {
    Route::get('all', 'EmployeeController@allEmployee');
    Route::get('endingcontracts', 'ContractController@employeesAtEndOfContract');
    Route::get('paginated/{n}', 'EmployeeController@allEmployeeWithAttributesPaginated');
    Route::get('onleave', 'EmployeeController@employeesOnLeave');
    Route::get('allwithattributes', 'EmployeeController@allEmployeeWithAttributes');
    Route::get('allwithsalary/{startDate}/{endDate}', 'EmployeeController@allEmployeeWithSalary');
    Route::get('undercontract', 'EmployeeController@allEmployeeUnderContract');
    Route::get('notundercontract', 'EmployeeController@allEmployeeNotUnderContract');
    Route::get('withoutgroup', 'EmployeeController@allEmployeeWithoutGroup');
    Route::get('single/{id}', 'EmployeeController@getEmployee');
    Route::get('count', 'EmployeeController@count');
    Route::post('update/{id}', 'EmployeeController@updateEmployee');
    Route::post('enter', 'EmployeeController@newEmployeeEntry');
    Route::post('reenter', 'EmployeeController@oldEmployeeEntry');
    Route::post('exit', 'EmployeeController@employeeExit');
    Route::post('renewcontract/{id}', 'EmployeeController@renewContract');
});

// Leaves routes
Route::group(['prefix' => 'leave'], function () {
    Route::post('new', 'LeaveController@newLeave');
    Route::get('count', 'LeaveController@count');
    Route::get('current', 'LeaveController@currentLeaves');
    Route::get('future', 'LeaveController@futureLeaves');
    Route::get('remainings', 'LeaveController@remainingLeavesPerEmployee');
    Route::post('delete/{id}', 'LeaveController@cancelLeave');
});

// Loan routes
Route::group(['prefix' => 'loan'], function () {
    Route::post('new', 'LoanController@newLoan');
    Route::get('count', 'LoanController@actualLoanCount');
    Route::post('delete/{id}', 'LoanController@deleteLoan');
    Route::get('loanPaid/{id}', 'LoanController@loanPaid');
    Route::get('listactual', 'LoanController@actualLoanList');
});

// Advance routes
Route::group(['prefix' => 'advance'], function () {
    Route::post('new', 'AdvanceController@newAdvance');
    Route::get('count', 'AdvanceController@actualAdvanceCount');
    Route::post('delete/{id}', 'AdvanceController@deleteAdvance');
    Route::get('loanPaid/{id}', 'AdvanceController@AdvancePaid');
    Route::get('listactual', 'AdvanceController@actualAdvanceList');
});

//property Routes
Route::group(['prefix' => 'property'], function () {
    Route::get('all', 'PropertyController@all');
    Route::post('new', 'PropertyController@newProperty');
    Route::get('delete/{id}', 'PropertyController@deleteProperty');
    Route::post('modify/{id}', 'PropertyController@modifyProperty');
    Route::post('massDelete', 'PropertyController@massDelete');
});


//Elements Routes
Route::group(['prefix' => 'element'], function () {
    Route::get('primes', 'ElementController@primes');
    Route::get('deductions', 'ElementController@deductions');
    Route::post('new', 'ElementController@newElement');
    Route::get('delete/{id}', 'ElementController@deleteElement');
    Route::post('modify/{id}', 'ElementController@modifyElement');
    Route::post('massDelete', 'ElementController@massDelete');
});

//Payroll Routes
Route::group(['prefix' => 'payroll'], function () {
    Route::post('get', 'PayrollController@get');
    Route::post('recompute', 'PayrollController@recompute');
    Route::get('validate/{id}', 'PayrollController@validateSalary');
    Route::post('modify/{id}', 'PayrollController@modifySalary');
    Route::get('print/{id}/{startDate}/{endDate}', 'PayrollController@printSalary');
    Route::get('printweb/{id}/{startDate}/{endDate}', 'PayrollController@printSalaryWeb');
    Route::get('printall/{startDate}/{endDate}', 'PayrollController@printAllSalary');
});

//Leave type routes
Route::group(['prefix' => 'leavetype'], function () {
    Route::post('new', 'LeaveTypeController@newLeaveType');
    Route::get('list', 'LeaveTypeController@leaveTypeList');
    Route::post('modify/{id}', 'LeaveTypeController@modifyLeaveType');
    Route::get('delete/{id}', 'LeaveTypeController@deleteLeaveType');
    Route::post('massdelete', 'LeaveTypeController@deleteLeaveType');
});

//Group Routes
Route::group(['prefix' => 'group'], function () {
    Route::post('new', 'GroupController@newGroup');
    Route::post('update', 'GroupController@updateGroup');
    Route::get('list', 'GroupController@groupList');
    Route::get('details/{id}', 'GroupController@groupDetails');
    Route::get('delete/{id}', 'GroupController@deleteGroup');
});


//User Routes
Route::group(['prefix' => 'user'], function () {
    Route::post('new', 'UserController@addUser');
    Route::post('modify/{id}', 'UserController@modifyUser');
    Route::get('delete/{id}', 'UserController@deleteUser');
    Route::get('list', 'UserController@userList');
    Route::post('changefirstpwd/{id}', 'UserController@changeFirstPassword');
    Route::post('changepwd/{id}', 'UserController@changePassword');
    Route::post('changeemail/{id}', 'UserController@changeEmail');
});

//Settings routes
Route::get('settings', 'SettingsController@get');
Route::post('settings/modify', 'SettingsController@modify');
Route::post('settings/tcs', 'SettingsController@modifyPayrollTax');
Route::post('settings/cnss', 'SettingsController@modifySocialSecuritySettings');
Route::post('settings/irpp', 'SettingsController@modifyPersonnalIncomeTax');
Route::post('settings/seniority', 'SettingsController@modifySeniorityBonus');
Route::post('settings/others', 'SettingsController@modifyOtherSettings');

//test
Route::get('test', 'PayrollController@test');
Route::post('t', 'UtilsController@parsexml');
Route::get('tt', 'UtilsController@parsexml');

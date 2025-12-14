<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\DashBoard\AuthController;
use App\Http\Controllers\DashBoard\DashboardController;
use App\Http\Controllers\ParentStudent\RegisterParentStudentController;
use App\Http\Controllers\ParentStudent\ParentDashboardController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\CheckStatusStudent;
use App\Http\Controllers\Student\CheckStatusStudentController;
use App\Http\Controllers\Student\RegisterStudentsController;
use App\Http\Controllers\Student\StudentVerifyController;
use App\Http\Controllers\Student\SubmitOnboardingFormController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\VnpayTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('registerstudents', [RegisterStudentsController::class, 'registerStudent']);
Route::get('parentdashboard', [ParentDashboardController::class, 'index']);
Route::post('registerparentstudent', [RegisterParentStudentController::class, 'registerParentStudent']);
Route::get('newspapers', [\App\Http\Controllers\NewspaperController::class, 'index']);
Route::get('newspapers/{id}', [\App\Http\Controllers\NewspaperController::class, 'show']);

Route::post('/chat', [ChatController::class, 'send']);

Route::post('/vnpay/create', [VnpayController::class, 'createPayment']);
Route::get('/vnpay/return', [VnpayController::class, 'vnpReturn']);

Route::prefix('student')->middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::post('submitonboardingform', [SubmitOnboardingFormController::class, 'submitOnboardingForm']);
    Route::post('checkstatusstudent', [CheckStatusStudentController::class, 'checkStatusStudent']);
    Route::post('verifystudent', [StudentVerifyController::class, 'verifyStudent']);
    Route::get('displayroom', [StudentVerifyController::class, 'displayRoom']);
    Route::get('showroomstudent', [StudentController::class, 'showMyRoom']);
    Route::get('showmypayments', [StudentController::class, 'showMyPayments']);
    Route::post('postdeparturerequest', [StudentController::class, 'postDepartureRequest']);
    Route::get('checkdeparturerequest', [StudentController::class, 'checkDepartureRequest']);
    Route::get('showmycontract', [StudentController::class, 'showMyContract']);
    Route::get('showroomfacilities', [StudentController::class, 'showRoomFacilities']);
    Route::post('reportfacility', [StudentController::class, 'reportFacility']);
    Route::get('checkoverduepayments', [StudentController::class, 'checkOverduePayments']);
    Route::get('checkcontractstatus', [StudentController::class, 'checkContractStatus']);
    Route::post('extendcontract', [StudentController::class, 'extendContract']);
});

Route::prefix('parent')->middleware(['auth:sanctum', 'role:parent'])->group(function () {
    Route::get('dashboard', [ParentDashboardController::class, 'index']);
    Route::post('/receive-refund/{payment_id}', [ParentDashboardController::class, 'receiveRefund']);
});


// Route::prefix('admin')->group(function () {
//     Route::post('login', [AuthController::class, 'login']);
// });

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin|staff'])->group(function () {

    Route::resource('student', \App\Http\Controllers\DashBoard\StudentController::class);
    Route::resource('facility', \App\Http\Controllers\DashBoard\FacilitiesController::class);
    Route::resource('payment', \App\Http\Controllers\DashBoard\PaymentController::class);
    Route::resource('room', \App\Http\Controllers\DashBoard\RoomController::class);
    Route::resource('contract', \App\Http\Controllers\DashBoard\ContractController::class);
    Route::resource('departure-request', \App\Http\Controllers\DashBoard\DepartureRequestController::class);
    Route::resource('user', \App\Http\Controllers\DashBoard\UserController::class);
    Route::resource('dormmanager', \App\Http\Controllers\DashBoard\DormManagerController::class);
    Route::resource('parentstudent', \App\Http\Controllers\DashBoard\ParentStudentController::class);
    Route::resource('schoolstudent', \App\Http\Controllers\DashBoard\SchoolStudentController::class);
    Route::resource('utilityprice', \App\Http\Controllers\DashBoard\UtilityPriceController::class);
    Route::resource('announcement', \App\Http\Controllers\DashBoard\NewPPController::class);
    Route::get('dashboard', [DashboardController::class, 'index']);
});

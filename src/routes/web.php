<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\ShowRequestsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {return view('index');});


Route::middleware('guest.admin')->group(function(){
    Route::get('/admin/login', [AdminAuthController::class, 'LoginForm']);
    Route::post('/admin/login', [AdminAuthController::class, 'login']);

});
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

// 一般会員のみ
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/start', [AttendanceController::class, 'start']);
    Route::post('/attendance/end', [AttendanceController::class, 'end']);
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart']);
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd']);

    Route::get('/attendance/list', [AttendanceController::class, 'show']);
    Route::get('/attendance/detail/{id?}', [AttendanceController::class, 'edit']);
    Route::post('/attendance/request-update/{id?}', [AttendanceController::class, 'requestUpdate']);
    Route::get('/stamp-correction-request/list', [AttendanceController::class, 'appry']);
});

// 管理者のみ
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/attendances', [AttendanceListController::class, 'index']);
    Route::get('/admin/attendances/{attendance?}', [AttendanceListController::class, 'edit']);

    Route::get('/admin/users', [StaffUserController::class, 'index']);
    Route::get('/admin/users/{user}/attendances', [StaffUserController::class, 'show']);

    Route::get('/admin/requests', [ShowRequestsController::class, 'index']);
    Route::get('/admin/requests/{id}', [ShowRequestsController::class, 'show']);
});

// Fortify準拠による設定で "/" はログインフォームへ
Route::get('/', function () {
    return redirect('/login');
});


/* Route::get('/', function () {
    return view('welcome');
}); 
*/

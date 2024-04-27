<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserRoleController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route for sending reset code via email
Route::post('/password/email', [PasswordResetController::class, 'sendResetCode']);
// Route for verifying reset code
Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
// Route for resetting password
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::get('/avatars/{filename}', function ($filename) {
    return response()->file(public_path('avatars/' . $filename));
})->name('avatar');

Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('api.verify-email');
Route::post('/register-committee', [AuthController::class, 'registerCommittee']);
Route::post('/register-employee', [AuthController::class, 'registerEmployee']);
Route::get('/comitee-employees', [EmployeeController::class, 'getComiteeEmployees']);
Route::get('/employees/with-roles', [EmployeeController::class, 'getAllEmployeesWithRole']);
Route::delete('employees/{id}', [EmployeeController::class, 'deleteEmployee']);

Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('user-roles', UserRoleController::class);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    
});

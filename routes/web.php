<?php

use App\Http\Controllers\Backend\ScormController;
use App\Http\Controllers\Backend\ContentManagementController;
use App\Http\Controllers\Backend\FileUploadController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Backend\DashboardController@index')->name('admin.dashboard');
    Route::resource('roles', 'Backend\RolesController', ['names' => 'admin.roles']);
    Route::resource('users', 'Backend\UsersController', ['names' => 'admin.users']);
    Route::resource('admins', 'Backend\AdminsController', ['names' => 'admin.admins']);

    Route::get('/scorm/users-report/{id}', [ScormController::class, 'userReport'])->name('scorm.report');

    Route::post('/save/scorm-data', [ScormController::class, 'saveProgress'])->name('progress.save');

    Route::resource('scorm', ScormController::class, ['names' => 'admin.scorm']);



    Route::post('/get/scorm-data', [ScormController::class, 'scormData'])->name('scorm.get');


    // Login Routes
    Route::get('/login', 'Backend\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('/login/submit', 'Backend\Auth\LoginController@login')->name('admin.login.submit');

    // Logout Routes
    Route::post('/logout/submit', 'Backend\Auth\LoginController@logout')->name('admin.logout.submit');

    // Forget Password Routes
    Route::get('/password/reset', 'Backend\Auth\ForgetPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('/password/reset/submit', 'Backend\Auth\ForgetPasswordController@reset')->name('admin.password.update');

    Route::get('/content-management', [ContentManagementController::class,'index'])->name('content.index');
    Route::post('/store-content', [ContentManagementController::class,'store']);
    Route::delete('/content/{id}', [ContentManagementController::class, 'destroy'])->name('content.destroy');
    Route::post('/temp-upload', [ContentManagementController::class,'tmpUpload']);
    Route::delete('/temp-delete', [ContentManagementController::class,'tmpDelete']);

    Route::get('file-upload', [FileUploadController::class, 'index'])->name('files.index');
    // Route::post('file-upload/upload-large-files', [FileUploadController::class, 'uploadLargeFiles'])->name('files.upload.large');
    Route::post('admin/upload-file', [FileUploadController::class, 'uploadLarge'])->name('files.upload.large');


    Route::get('upload', [FileUploadController::class, 'index'])->name('upload.index');
    Route::post('upload', [FileUploadController::class, 'store'])->name('upload.store');

});

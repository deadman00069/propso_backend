<?php

use App\Http\Controllers\api\CityController;
use App\Http\Controllers\api\LocationController;
use App\Http\Controllers\api\PropertyCategoryController;
use App\Http\Controllers\api\PropertyTypeController;
use App\Http\Controllers\api\FacilityController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\KycController;
use App\Http\Controllers\api\PropertyController;
use Illuminate\Support\Facades\Route;

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

// All protected apis
Route::post('sign-up', [UserController::class, 'signUp']);
Route::post('login', [UserController::class, 'login']);
Route::post('admin-login', [UserController::class, 'adminLogin']);
Route::post('get-phone', [UserController::class, 'getPhoneNo']);
Route::post('check-token', [UserController::class, 'checkIfTokenValid']);
Route::get('get-all-city', [CityController::class, 'getAllCities']);
Route::get('get-all-location', [LocationController::class, 'getAllLocation']);
Route::get('get-all-property-category', [PropertyCategoryController::class, 'getAllCategory']);
Route::get('get-all-property-type', [PropertyTypeController::class, 'getAllPropertyType']);
Route::get('get-all-facility', [FacilityController::class, 'getAllFacilities']);
Route::get('get-all-kyc', [KycController::class, 'getAllKyc']);
Route::post('get-all-property', [PropertyController::class, 'getAllProperty']);
Route::get('test', [KycController::class, 'createUid']);
Route::get('php_info', function (){
    return phpinfo();
});




// All protected apis
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('get-all-user', [UserController::class, 'getAllUsers']);
    Route::post('create-city', [CityController::class, 'createCity']);
    Route::post('delete-city', [CityController::class, 'deleteCity']);
    Route::post('create-location', [LocationController::class, 'createLocation']);
    Route::post('delete-location', [LocationController::class, 'deleteLocation']);
    Route::post('create-property-category', [PropertyCategoryController::class, 'createCategory']);
    Route::post('delete-property-category', [PropertyCategoryController::class, 'deleteCategory']);
    Route::post('create-property-type', [PropertyTypeController::class, 'createPropertyType']);
    Route::post('delete-property-type', [PropertyTypeController::class, 'deletePropertyType']);
    Route::post('create-facility', [FacilityController::class, 'createFacility']);
    Route::post('delete-facility', [FacilityController::class, 'deleteFacility']);
    Route::post('create-kyc', [KycController::class, 'createKyc']);
    Route::post('verify-kyc', [KycController::class, 'verifyKyc']);
    Route::post('create-property', [PropertyController::class, 'createProperty']);
});

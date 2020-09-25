<?php

use Illuminate\Http\Request;
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
Route::group(
    [
        'namespace' => 'App\Http\Controllers',
    ],
    static function () {
        Route::post('login', 'Auth\LoginController@login')->name('login');
        Route::post('register', 'Auth\RegisterController@register')->name('register');
        Route::post('logout', 'Auth\LoginController@logout')->name('logout');
        Route::group(
            ['prefix' => 'password'],
            static function ($api) {
                $api->post('email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name(
                    'password.email'
                );
                $api->post('reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
            }
        );
        Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
        Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name(
            'verification.verify'
        );
        Route::group(
            ['middleware' => 'auth'],
            function () {
                Route::get('contacts/import','ContactsController@import')->name('billi.import_contacts');
                Route::get('products/import','ProductsController@import')->name('billi.import_products');
                Route::resource('contacts','ContactsController');
                Route::resource('products','ProductsController');
                Route::resource('usersgroup','UsersGroupsController');
                Route::get(
                    '/user',
                    function (Request $request) {
                        return $request->user();
                    }
                );
                Route::get('test','Billy\BillyController@index')->name('test');
            }
        );
    }
);
Broadcast::routes(['middleware' => ['auth:sanctum']]);

<?php

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\AuthAdminController;
use App\Http\Controllers\Api\V1\Admin\AdminEmailChangesVerificationController;
use App\Http\Controllers\Api\V1\Admin\AdminFeatureController;
use App\Http\Controllers\Api\V1\Admin\ForgetAdminPasswordController;
use App\Http\Controllers\Api\V1\Article\AdminArticleController;
use App\Http\Controllers\Api\V1\Article\AdminArticleImagesController;
use App\Http\Controllers\Api\V1\Article\ArticleCommentController;
use App\Http\Controllers\Api\V1\Article\ArticleController;
use App\Http\Controllers\Api\V1\ArticleCategory\AdminArticleCategoryController;
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

Route::prefix('/v1')->middleware('verifyLanguage')->group(function () {

    // Article
    Route::get('/top-articles', [ArticleController::class, 'top']);
    Route::resource('/articles', ArticleController::class, ['only' => ['index', 'show']]);
    Route::post('/articles/{article}/comments', [ArticleCommentController::class, 'store']);

    Route::prefix('/admins')->group(function () {

        // Admin - Login
        Route::post('/login', [AuthAdminController::class, 'login']);
        Route::post('/resend-email-verification', [AuthAdminController::class, 'resendEmailVerification'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/email-verification/{token}', [AuthAdminController::class, 'emailVerification']);

        // Admin - Forget Password
        Route::post('/forget-password', [ForgetAdminPasswordController::class, 'forgetPassword'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/forget-password/verify-pin', [ForgetAdminPasswordController::class, 'verifyPin']);
        Route::post('/forget-password/reset-password', [ForgetAdminPasswordController::class, 'resetPassword']);
        Route::post('/verify-email-changes/{token}', AdminEmailChangesVerificationController::class);

        Route::group(['middleware' => ['auth:api_admin', 'scopes:admin', 'verifyUserActive']], function(){

            // Admin - Logout
            Route::post('/logout', [AuthAdminController::class, 'logout']);

            // Admin - Profile
            Route::get('/profile', [AdminProfileController::class, 'index']);
            Route::put('/profile', [AdminProfileController::class, 'update']);
            Route::post('/change-password', [AdminProfileController::class, 'changePassword']);

            // Admin - Article
            Route::post('/articles/{article}', [AdminArticleController::class, 'store']);
            Route::resource('/articles', AdminArticleController::class)->except('store');
            Route::post('/article-images', [AdminArticleImagesController::class, 'store']);

            // Admin - Article Category
            Route::resource('/article-categories', AdminArticleCategoryController::class);

            // Admin - Feature
            Route::get('/admin-features', [AdminFeatureController::class, 'index']);

            // Admin - Admin
            Route::get('', [AdminController::class, 'index']);
            Route::get('/{admin}', [AdminController::class, 'show']);
            Route::post('', [AdminController::class, 'store']);
            Route::put('/{admin}', [AdminController::class, 'update']);
            Route::delete('/{admin}', [AdminController::class, 'destroy']);
        });
    });
});

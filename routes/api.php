<?php

use App\Http\Controllers\Api\V1\Additional\AdminAdditionalController;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\AuthAdminController;
use App\Http\Controllers\Api\V1\Product\AdminProductController;
use App\Http\Controllers\Api\V1\Product\AdminProductImagesController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Address\UserAddressController;
use App\Http\Controllers\Api\V1\Admin\AdminEmailChangesVerificationController;
use App\Http\Controllers\Api\V1\Admin\AdminFeatureController;
use App\Http\Controllers\Api\V1\Admin\ForgetAdminPasswordController;
use App\Http\Controllers\Api\V1\AdminDashboardController;
use App\Http\Controllers\Api\V1\Article\AdminArticleController;
use App\Http\Controllers\Api\V1\Article\AdminArticleImagesController;
use App\Http\Controllers\Api\V1\Article\ArticleCommentController;
use App\Http\Controllers\Api\V1\Article\ArticleController;
use App\Http\Controllers\Api\V1\Contact\AdminContactController;
use App\Http\Controllers\Api\V1\Contact\ContactController;
use App\Http\Controllers\Api\V1\Newsletter\NewsletterEmailController;
use App\Http\Controllers\Api\V1\Order\UserOrderController;
use App\Http\Controllers\Api\V1\User\AdminUserController;
use App\Http\Controllers\Api\V1\User\Auth\AuthUserController;
use App\Http\Controllers\Api\V1\User\Auth\GoogleAuthUserController;
use App\Http\Controllers\Api\V1\User\ForgetPasswordController;
use App\Http\Controllers\Api\V1\User\UserProfileController;
use App\Http\Controllers\Api\V1\Log\AdminLogAdminController;
use App\Http\Controllers\Api\V1\Log\AdminLogUserController;
use App\Http\Controllers\Api\V1\Newsletter\AdminNewsletterController;
use App\Http\Controllers\Api\V1\Newsletter\AdminNewsletterEmailController;
use App\Http\Controllers\Api\V1\Order\AdminOrderController;
use App\Http\Controllers\Api\V1\Promo\AdminPromoController;
use App\Http\Controllers\Api\V1\User\UserDashboardController;
use App\Http\Controllers\Api\V1\OrderReview\UserOrderReviewController;
use App\Http\Controllers\Api\V1\Product\AdminProductVariantController;
use App\Http\Controllers\Api\V1\Product\ProductAdditionalController;
use App\Http\Controllers\Api\V1\Product\ProductReviewController;
use App\Http\Controllers\Api\V1\Location\AdminLocationController;
use App\Http\Controllers\Api\V1\Location\LocationController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\Order\OrderRefundController;
use App\Http\Controllers\Api\V1\Product\AdminProductFaqController;
use App\Http\Controllers\Api\V1\User\UserEmailChangesVerificationController;
use App\Http\Controllers\Api\V1\Webhook\Xendit\XenditInvoiceWebhookController;
use App\Http\Controllers\Api\V1\Webhook\Xendit\XenditPayoutLinkWebhookController;
use App\Http\Controllers\Api\V1\Webhook\Xendit\XenditRefundWebhookController;
use App\Http\Controllers\Api\V1\Wiki\AdminWikiController;
use App\Http\Controllers\Api\V1\Wiki\WikiController;
use App\Http\Controllers\Api\V1\Xendit\XeditChannelController;
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

Route::prefix('/v1')->middleware('verifyLanguage')->group(function () {

    // Product
    Route::get('/products/{product}/additionals', [ProductAdditionalController::class, 'index']);
    Route::get('/products/{product}/more', [ProductController::class, 'more']);
    Route::get('/products/{product}/reviews', [ProductReviewController::class, 'index']);
    Route::get('/products/{product}/reviews/{review}', [ProductReviewController::class, 'show'])->scopeBindings();
    Route::resource('/products', ProductController::class, ['only' => ['index', 'show']]);
    Route::get('/top-products', [ProductController::class, 'top']);

    // Article
    Route::get('/top-articles', [ArticleController::class, 'top']);
    Route::resource('/articles', ArticleController::class, ['only' => ['index', 'show']]);
    Route::post('/articles/{article}/comments', [ArticleCommentController::class, 'store']);

    // Newsletter Email
    Route::post('/newsletter-emails', [NewsletterEmailController::class, 'store']);

    // Wiki
    Route::resource('/wikis', WikiController::class, ['only' => ['index', 'show']]);

    // Contact
    Route::post('/contacts', [ContactController::class, 'store']);

    // Webhook
    Route::post('/xendit-webhook/invoice', XenditInvoiceWebhookController::class);
    Route::post('/xendit-webhook/payout', XenditRefundWebhookController::class);
    Route::post('/xendit-webhook/payout-link', XenditPayoutLinkWebhookController::class);

    // Location
    Route::get('/locations', [LocationController::class, 'index']);

    // Xendit
    Route::post('/refund-channels', XeditChannelController::class);

    // Order
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/refund', [OrderController::class, 'refund']);
    Route::resource('/orders', OrderController::class)->only(['index', 'show', 'store']);

    // Refund
    Route::post('/orders/{order}/refund-request', [OrderRefundController::class, 'request']);
    Route::post('/orders/{order}/refund', [OrderRefundController::class, 'refund']);

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

        Route::get('/orders/export-excel', [AdminOrderController::class, 'exportExcel']);

        Route::group(['middleware' => ['auth:api_admin', 'scopes:admin', 'verifyUserActive']], function(){

            //Dashboard
            Route::get('/dashboard', AdminDashboardController::class);
            // Route::post('/user-statistic', AdminUserStatisticController::class);

            // Route::prefix('/{admin}')->middleware(['verifyCompanyParameter'])->scopeBindings()->group(function () {

            // Admin - Logout
            Route::post('/logout', [AuthAdminController::class, 'logout']);

            // Admin - Profile
            Route::get('/profile', [AdminProfileController::class, 'index']);
            Route::put('/profile', [AdminProfileController::class, 'update']);
            Route::post('/change-password', [AdminProfileController::class, 'changePassword']);

            // Admin - Users
            Route::resource('/users', AdminUserController::class, ['except' => ['store']]);

            // Admin - Location
            Route::get('/location-list', [AdminLocationController::class, 'list']);
            Route::resource('/locations', AdminLocationController::class);

            // Admin - Product
            Route::resource('/products/{product}/variants', AdminProductVariantController::class);
            Route::get('/list-unvaried-products', [AdminProductController::class, 'listUnvaried']);
            Route::resource('/products', AdminProductController::class);
            Route::resource('/products/{product}/faqs', AdminProductFaqController::class)->only(['index', 'store']);
            Route::post('/products/{product}/delete-faqs', [AdminProductFaqController::class, 'destroy']);
            Route::resource('/products/{product}/images', AdminProductImagesController::class)->only(['index', 'store']);
            Route::post('/products/{product}/save-images', [AdminProductImagesController::class, 'save']);
            Route::get('/product-list', [AdminProductController::class, 'list']);

            // Admin - Article
            Route::resource('/articles', AdminArticleController::class);
            Route::post('/article-images', [AdminArticleImagesController::class, 'store']);

            //Admin - Newsletter
            Route::resource('/newsletters', AdminNewsletterController::class)->only(['index', 'show', 'store']);

            // Admin - Newsletter Email
            Route::get('/newsletter-emails', [AdminNewsletterEmailController::class, 'index']);
            Route::delete('/newsletter-emails/{email}', [AdminNewsletterEmailController::class, 'destroy']);

            // Admin - Additional
            Route::get('/additional-list', [AdminAdditionalController::class, 'list']);
            Route::resource('/additionals', AdminAdditionalController::class);

            // Admin - Wiki
            Route::resource('/wikis', AdminWikiController::class);

            // Admin - Contact
            // Route::post('/contacts/{contact}/reply', [AdminContactController::class, 'reply']);
            Route::resource('/contacts', AdminContactController::class)->only(['index', 'show', 'destroy']);

            // Admin - Promo
            Route::resource('/promos', AdminPromoController::class);

            // Admin - Log
            Route::get('/user-log', [AdminLogUserController::class, 'index']);
            Route::get('/admin-log', [AdminLogAdminController::class, 'index']);

            // Admin - User
            Route::resource('/users', AdminUserController::class)->only(['index', 'show', 'update']);

            // Admin - Feature
            Route::get('/admin-features', [AdminFeatureController::class, 'index']);

            // Admin - Order
            Route::get('/orders', [AdminOrderController::class, 'index']);
            Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
            Route::put('/orders/{order}', [AdminOrderController::class, 'update']);
            Route::post('/orders/{order}/confirm', [AdminOrderController::class, 'confirm']);
            Route::post('/orders/{order}/send-payment', [AdminOrderController::class, 'sendPayment']);
            Route::post('/orders/{order}/decline', [AdminOrderController::class, 'decline']);


            // Admin - Admin
            Route::get('', [AdminController::class, 'index']);
            Route::get('/{admin}', [AdminController::class, 'show']);
            Route::post('', [AdminController::class, 'store']);
            Route::put('/{admin}', [AdminController::class, 'update']);
            Route::delete('/{admin}', [AdminController::class, 'destroy']);
        });
    });

    Route::prefix('/users')->group(function () {

        // User - Login
        Route::post('/login', [AuthUserController::class, 'login']);
        Route::post('/register', [AuthUserController::class, 'register'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/resend-email-verification', [AuthUserController::class, 'resendEmailVerification'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/email-verification/{token}', [AuthUserController::class, 'emailVerification']);
        Route::post('/google/register', [GoogleAuthUserController::class, 'register']);
        Route::post('/google/login', [GoogleAuthUserController::class, 'login']);

        // User - Forget Password
        Route::post('/forget-password', [ForgetPasswordController::class, 'forgetPassword'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/forget-password/verify-pin', [ForgetPasswordController::class, 'verifyPin']);
        Route::post('/forget-password/reset-password', [ForgetPasswordController::class, 'resetPassword']);
        Route::post('/verify-email-changes/{token}', UserEmailChangesVerificationController::class);

        Route::group(['middleware' => ['auth:api_user', 'scopes:user', 'verifyUserActive']], function(){

            // User - Logout
            Route::post('/logout', [AuthUserController::class, 'logout']);

            // //Dashboard
            // Route::get('/dashboard', AdminDashboardController::class);
            // Route::post('/user-statistic', AdminUserStatisticController::class);

            // Profile
            Route::get('/profile', [UserProfileController::class, 'index']);
            Route::put('/profile', [UserProfileController::class, 'update']);
            Route::post('/change-password', [UserProfileController::class, 'changePassword']);
            Route::post('/change-language', [UserProfileController::class, 'changeLanguage']);

            // Profile
            Route::get('/dashboard', UserDashboardController::class);

        });

        Route::prefix('/{user}')->scopeBindings()->group(function () {
            Route::group(['middleware' => ['auth:api_user', 'scopes:user', 'verifyUserActive']], function(){
                // Riview
                Route::resource('/order-reviews', UserOrderReviewController::class)->only(['index', 'show', 'store', 'destroy']);

                // Xendit
                Route::post('/refund-channels', XeditChannelController::class);

                // Order
                Route::post('/orders/{order}/cancel', [UserOrderController::class, 'cancel']);
                Route::post('/orders/{order}/refund', [UserOrderController::class, 'refund']);
                Route::get('/orders/{order}/refund-channels', [UserOrderController::class, 'refundChanel']);
                Route::resource('/orders', UserOrderController::class)->only(['index', 'show', 'store']);
            });
        });
    });
});

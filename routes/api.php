<?php

use App\Http\Controllers\Api\V1\ActivityOrder\ActivityOrderRefundController;
use App\Http\Controllers\Api\V1\ActivityOrder\AdminActivityOrderController;
use App\Http\Controllers\Api\V1\ActivityOrder\UserActivityOrderController;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AdminEmailChangesVerificationController;
use App\Http\Controllers\Api\V1\Admin\AdminFeatureController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\AuthAdminController;
use App\Http\Controllers\Api\V1\Admin\ForgetAdminPasswordController;
use App\Http\Controllers\Api\V1\ArticleCategory\AdminArticleCategoryController;
use App\Http\Controllers\Api\V1\Article\AdminArticleController;
use App\Http\Controllers\Api\V1\Article\AdminArticleImagesController;
use App\Http\Controllers\Api\V1\Article\ArticleCommentController;
use App\Http\Controllers\Api\V1\Article\ArticleController;
use App\Http\Controllers\Api\V1\Exclude\AdminExcludeController;
use App\Http\Controllers\Api\V1\Faq\AdminFaqController;
use App\Http\Controllers\Api\V1\Faq\FaqController;
use App\Http\Controllers\Api\V1\FaqCategory\AdminFaqCategoryController;
use App\Http\Controllers\Api\V1\FaqCategory\FaqCategoryController;
use App\Http\Controllers\Api\V1\FastBoatOrder\AdminFastBoatOrderController;
use App\Http\Controllers\Api\V1\FastBoatOrder\FastBoatOrderRefundController;
use App\Http\Controllers\Api\V1\Harbor\AdminHarborController;
use App\Http\Controllers\Api\V1\Harbor\HarborController;
use App\Http\Controllers\Api\V1\Include\AdminIncludeController;
use App\Http\Controllers\Api\V1\Itinerary\AdminItineraryController;
use App\Http\Controllers\Api\V1\Location\AdminLocationController;
use App\Http\Controllers\Api\V1\Location\LocationController;
use App\Http\Controllers\Api\V1\FastBoatOrder\UserFastBoatOrderController;
use App\Http\Controllers\Api\V1\PrivateBoatOrder\AdminPrivateBoatOrderController;
use App\Http\Controllers\Api\V1\PrivateBoatOrder\PrivateBoatOrderRefundController;
use App\Http\Controllers\Api\V1\PrivateBoatOrder\UserPrivateBoatOrderController;
use App\Http\Controllers\Api\V1\Product\FastBoat\AdminFastBoatController;
use App\Http\Controllers\Api\V1\Product\FastBoat\AdminScheduleController;
use App\Http\Controllers\Api\V1\Product\FastBoat\FastBoatController;
use App\Http\Controllers\Api\V1\Product\PrivateBoat\AdminPrivateBoatController;
use App\Http\Controllers\Api\V1\Product\PrivateBoat\AdminPrivateBoatImageController;
use App\Http\Controllers\Api\V1\Product\PrivateBoat\PrivateBoatController;
use App\Http\Controllers\Api\V1\Product\Activity\AdminActivityController;
use App\Http\Controllers\Api\V1\Product\Activity\AdminActivityImageController;
use App\Http\Controllers\Api\V1\Product\Activity\ActivityController;
use App\Http\Controllers\Api\V1\Product\FastBoat\FastBoatScheduleController;
use App\Http\Controllers\Api\V1\Promo\AdminPromoController;
use App\Http\Controllers\Api\V1\Promo\UserPromoController;
use App\Http\Controllers\Api\V1\Tag\AdminTagController;
use App\Http\Controllers\Api\V1\Ticket\UserTicketController;
use App\Http\Controllers\Api\V1\User\AdminUserController;
use App\Http\Controllers\Api\V1\User\Auth\AuthUserController;
use App\Http\Controllers\Api\V1\User\ForgetPasswordController;
use App\Http\Controllers\Api\V1\User\UserProfileController;
use App\Http\Controllers\Api\V1\Webhook\Xendit\XenditInvoiceWebhookController;
use App\Http\Controllers\Api\V1\Webhook\Xendit\XenditPayoutLinkWebhookController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/v1')->middleware('verifyLanguage')->group(function () {

    // Public - FAQ Category
    Route::get('/faq-categories', [FaqCategoryController::class, 'index']);
    Route::get('/faq-categories/{faqCategory}', [FaqCategoryController::class, 'show']);

    // Public - FAQ
    Route::get('/faqs-top', [FaqController::class, 'top']);

    // Webhook
    Route::post('/xendit-webhook/invoice', XenditInvoiceWebhookController::class);
    Route::post('/xendit-webhook/payout-link', XenditPayoutLinkWebhookController::class);

    // Public - FastBoat
    Route::get('/fast-boats', [FastBoatController::class, 'index']);

    // Public - FastBoat Schedule
    Route::get('/fast-boat-schedules-first', [FastBoatScheduleController::class, 'firstSearch']);
    Route::get('/fast-boat-schedules', [FastBoatScheduleController::class, 'index']);

    // Public - Activity
    Route::get('/top-activities', [ActivityController::class, 'top']);
    Route::get('/activities', [ActivityController::class, 'search']);
    Route::get('/activities/{activity:slug}', [ActivityController::class, 'show']);

    // Public - Harbor
    Route::get('/harbors', [HarborController::class, 'index']);

    // Public - Location
    Route::get('/locations', [LocationController::class, 'index']);

    // Public - Article
    Route::get('/top-articles', [ArticleController::class, 'top']);
    Route::resource('/articles', ArticleController::class, ['only' => ['index', 'show']]);
    Route::post('/articles/{article}/comments', [ArticleCommentController::class, 'store']);

    // Public - PrivateBoat
    Route::get('/top-private-boats', [PrivateBoatController::class, 'top']);
    Route::get('/private-boats', [PrivateBoatController::class, 'search']);
    Route::get('/private-boats/{privateBoat:slug}', [PrivateBoatController::class, 'show']);

    // FastBoat - ticket
    Route::get('/fast-boat-orders/{fastBoatOrder}/ticket/', [UserTicketController::class, 'pdf']);

    //Admin routes
    Route::prefix('admins')->middleware('verifyLanguage')->group(function () {

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

            // Admin - Promo
            Route::resource('/promos', AdminPromoController::class);

            // Admin - Article
            Route::resource('/articles', AdminArticleController::class);
            Route::post('/article-images', [AdminArticleImagesController::class, 'store']);

            // Admin - Article Category
            Route::resource('/article-categories', AdminArticleCategoryController::class);

            // Admin - Harbor
            Route::get('/harbors', [AdminHarborController::class, 'index']);
            Route::post('/harbors', [AdminHarborController::class, 'store']);
            Route::get('/harbors-all', [AdminHarborController::class, 'all']);
            Route::get('/harbors/{harbor}', [AdminHarborController::class, 'show']);
            Route::put('/harbors/{harbor}', [AdminHarborController::class, 'update']);
            Route::delete('/harbors/{harbor}', [AdminHarborController::class, 'destroy']);

            // Admin - Location
            Route::get('/locations', [AdminLocationController::class, 'index']);
            Route::post('/locations', [AdminLocationController::class, 'store']);
            Route::get('/locations-all', [AdminLocationController::class, 'all']);
            Route::get('/locations/{location}', [AdminLocationController::class, 'show']);
            Route::put('/locations/{location}', [AdminLocationController::class, 'update']);
            Route::delete('/locations/{location}', [AdminLocationController::class, 'destroy']);

            // Admin - Include
            Route::get('/includes', [AdminIncludeController::class, 'index']);
            Route::post('/includes', [AdminIncludeController::class, 'store']);
            Route::get('/includes-all', [AdminIncludeController::class, 'all']);
            Route::get('/includes/{include}', [AdminIncludeController::class, 'show']);
            Route::put('/includes/{include}', [AdminIncludeController::class, 'update']);
            Route::delete('/includes/{include}', [AdminIncludeController::class, 'destroy']);

            // Admin - Exclude
            Route::get('/excludes', [AdminExcludeController::class, 'index']);
            Route::post('/excludes', [AdminExcludeController::class, 'store']);
            Route::get('/excludes-all', [AdminExcludeController::class, 'all']);
            Route::get('/excludes/{exclude}', [AdminExcludeController::class, 'show']);
            Route::put('/excludes/{exclude}', [AdminExcludeController::class, 'update']);
            Route::delete('/excludes/{exclude}', [AdminExcludeController::class, 'destroy']);

            // Admin - Itinerrary
            Route::get('/itineraries', [AdminItineraryController::class, 'index']);
            Route::post('/itineraries', [AdminItineraryController::class, 'store']);
            Route::get('/itineraries-all', [AdminItineraryController::class, 'all']);
            Route::get('/itineraries/{itinerary}', [AdminItineraryController::class, 'show']);
            Route::put('/itineraries/{itinerary}', [AdminItineraryController::class, 'update']);
            Route::delete('/itineraries/{itinerary}', [AdminItineraryController::class, 'destroy']);

            // Admin - Tag
            Route::get('/tags', [AdminTagController::class, 'index']);
            Route::get('/tags-all', [AdminTagController::class, 'all']);
            Route::get('/tags/{tag}', [AdminTagController::class, 'show']);
            Route::post('/tags', [AdminTagController::class, 'store']);
            Route::put('/tags/{tag}', [AdminTagController::class, 'update']);
            Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy']);

            // Admin - Faq Category
            Route::get('/faq-categories', [AdminFaqCategoryController::class, 'index']);
            Route::post('/faq-categories', [AdminFaqCategoryController::class, 'store']);
            Route::get('/faq-categories-all', [AdminFaqCategoryController::class, 'all']);
            Route::get('/faq-categories/{faqCategory}', [AdminFaqCategoryController::class, 'show']);
            Route::put('/faq-categories/{faqCategory}', [AdminFaqCategoryController::class, 'update']);
            Route::delete('/faq-categories/{faqCategory}', [AdminFaqCategoryController::class, 'destroy']);

            // Admin - Faq
            Route::get('/faqs', [AdminFaqController::class, 'index']);
            Route::get('/faqs/{faq}', [AdminFaqController::class, 'show']);
            Route::post('/faqs', [AdminFaqController::class, 'store']);
            Route::put('/faqs/{faq}', [AdminFaqController::class, 'update']);
            Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy']);

            // Admin - FastBoat
            Route::get('/fast-boats', [AdminFastBoatController::class, 'index']);
            Route::post('/fast-boats', [AdminFastBoatController::class, 'store']);
            Route::get('/fast-boats/{fastBoat:slug}', [AdminFastBoatController::class, 'show']);
            Route::put('/fast-boats/{fastBoat:slug}', [AdminFastBoatController::class, 'update']);
            Route::delete('/fast-boats/{fastBoat:slug}', [AdminFastBoatController::class, 'destroy']);

            // Admin - FastBoat Schedule
            Route::get('/fast-boats/{fastBoat:slug}/schedules', [AdminScheduleController::class, 'index']);
            Route::get('/fast-boats/{fastBoat:slug}/schedules/{schedule}', [AdminScheduleController::class, 'show'])->scopeBindings();
            Route::post('/fast-boats/{fastBoat:slug}/schedules', [AdminScheduleController::class, 'store']);
            Route::put('/fast-boats/{fastBoat:slug}/schedules/{schedule}', [AdminScheduleController::class, 'update'])->scopeBindings();
            Route::delete('/fast-boats/{fastBoat:slug}/schedules/{schedule}', [AdminScheduleController::class, 'destroy'])->scopeBindings();

            // Admin - PrivateBoat
            Route::get('/private-boats', [AdminPrivateBoatController::class, 'index']);
            Route::post('/private-boats', [AdminPrivateBoatController::class, 'store']);
            Route::get('/private-boats/{privateBoat:slug}', [AdminPrivateBoatController::class, 'show']);
            Route::put('/private-boats/{privateBoat:slug}', [AdminPrivateBoatController::class, 'update']);
            Route::delete('/private-boats/{privateBoat:slug}', [AdminPrivateBoatController::class, 'destroy']);

            // Admin - PrivateBoat Image
            Route::get('/private-boats/{privateBoat:slug}/images', [AdminPrivateBoatImageController::class, 'index']);
            Route::post('/private-boats/{privateBoat:slug}/images', [AdminPrivateBoatImageController::class,'store']);
            Route::post('/private-boats/{privateBoat:slug}/save-images', [AdminPrivateBoatImageController::class, 'saveImages']);

            // Admin - Activity
            Route::get('/activities', [AdminActivityController::class, 'index']);
            Route::post('/activities', [AdminActivityController::class, 'store']);
            Route::get('/activities/{activity:slug}', [AdminActivityController::class, 'show']);
            Route::put('/activities/{activity:slug}', [AdminActivityController::class, 'update']);
            Route::delete('/activities/{activity:slug}', [AdminActivityController::class, 'destroy']);

            // Admin - Activity Image
            Route::get('/activities/{activity:slug}/images', [AdminActivityImageController::class, 'index']);
            Route::post('/activities/{activity:slug}/images', [AdminActivityImageController::class,'store']);
            Route::post('/activities/{activity:slug}/save-images', [AdminActivityImageController::class, 'saveImages']);

            // Admin - Fast Boat Order
            Route::get('/fast-boat-orders', [AdminFastBoatOrderController::class, 'index']);
            Route::get('/fast-boat-orders/{order}', [AdminFastBoatOrderController::class, 'show']);

            // Admin - Private Boat Order
            Route::get('/private-boat-orders', [AdminPrivateBoatOrderController::class, 'index']);
            Route::get('/private-boat-orders/{order}', [AdminPrivateBoatOrderController::class, 'show']);

            // Admin - Activity Order
            Route::get('/activity-orders', [AdminActivityOrderController::class, 'index']);
            Route::get('/activity-orders/{order}', [AdminActivityOrderController::class, 'show']);

            // Admin - Promo
            Route::resource('/promos', AdminPromoController::class);

            // Admin - User
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::put('/users/{user:uuid}', [AdminUserController::class, 'update']);

            // Admin - Feature
            Route::get('/admin-features', [AdminFeatureController::class, 'index']);

            // Admin - Admin
            Route::get('/', [AdminController::class, 'index']);
            Route::get('/{admin}', [AdminController::class, 'show']);
            Route::post('', [AdminController::class, 'store']);
            Route::put('/{admin}', [AdminController::class, 'update']);
            Route::delete('/{admin}', [AdminController::class, 'destroy']);
        });
    });

    //User routes
    Route::prefix('/users')->group(function(){

        //Authentication
        Route::post('/register', [AuthUserController::class,'register']);
        Route::post('/resend-email-verification', [AuthUserController::class, 'resendEmailVerification'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/email-verification/{token}', [AuthUserController::class, 'emailVerification']);
        Route::post('/login', [AuthUserController::class, 'login']);

        //password controller
        Route::post('/password/forget', [ForgetPasswordController::class, 'forgetPassword']);
        Route::post('/password/forget/verify-otp', [ForgetPasswordController::class, 'verifyPin']);
        Route::post('/password/reset/', [ForgetPasswordController::class, 'resetPassword']);
        Route::get('/password/forget/resend', [ForgetPasswordController::class,'forgetPassword']);

        Route::middleware(['auth:api_user'])->group(function(){
            //User Profile
            Route::get('/profile', [UserProfileController::class, 'index']);
            Route::put('/profile', [UserProfileController::class, 'update']);
            Route::post('/change-password', [UserProfileController::class, 'changePassword']);
            Route::post('/change-language', [UserProfileController::class, 'changeLanguage']);

            // Promo
            Route::post('/apply-promo', UserPromoController::class);
        });

        Route::prefix('/{user}')->scopeBindings()->group(function () {
            Route::middleware(['auth:api_user'])->group(function(){

                //Authentication
                Route::post('/logout', [AuthUserController::class, 'logout']);

                //password controller
                Route::post('/change-password', [UserProfileController::class, 'changePassword']);

                // Fast Boat Order
                Route::get('/fast-boat-orders', [UserFastBoatOrderController::class, 'index']);
                Route::get('/fast-boat-orders/{fastBoatOrder}', [UserFastBoatOrderController::class, 'show']);
                Route::post('/fast-boat-orders', [UserFastBoatOrderController::class, 'store']);
                Route::post('/fast-boat-orders/{fastBoatOrder}/cancel', [UserFastBoatOrderController::class, 'cancel']);
                Route::post('/fast-boat-orders/{fastBoatOrder}/refund-request', [FastBoatOrderRefundController::class, 'request']);
                Route::post('/fast-boat-orders/{fastBoatOrder}/refund', [FastBoatOrderRefundController::class, 'refund']);

                // Private Boat Order
                Route::get('/private-boat-orders', [UserPrivateBoatOrderController::class, 'index']);
                Route::get('/private-boat-orders/{privateBoatOrder}', [UserPrivateBoatOrderController::class, 'show']);
                Route::post('/private-boat-orders', [UserPrivateBoatOrderController::class, 'store']);
                Route::post('/private-boat-orders/{privateBoatOrder}/cancel', [UserPrivateBoatOrderController::class, 'cancel']);
                Route::post('/private-boat-orders/{privateBoatOrder}/refund-request', [PrivateBoatOrderRefundController::class, 'request']);
                Route::post('/private-boat-orders/{privateBoatOrder}/refund', [PrivateBoatOrderRefundController::class, 'refund']);

                // Activity Order
                Route::get('/activity-orders', [UserActivityOrderController::class, 'index']);
                Route::get('/activity-orders/{activityOrder}', [UserActivityOrderController::class, 'show']);
                Route::post('/activity-orders', [UserActivityOrderController::class, 'store']);
                Route::post('/activity-orders/{activityOrder}/cancel', [UserActivityOrderController::class, 'cancel']);
                Route::post('/activity-orders/{activityOrder}/refund-request', [ActivityOrderRefundController::class, 'request']);
                Route::post('/activity-orders/{activityOrder}/refund', [ActivityOrderRefundController::class, 'refund']);
            });
        });
    });
});

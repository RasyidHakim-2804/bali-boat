<?php

use App\Http\Controllers\Api\V1\Order\WebhookOrderController;
use App\Http\Controllers\Web\V1\AgentIdentityPictureController;
use App\Http\Controllers\Web\V1\Order\OrderResponseController;
use App\Http\Controllers\Web\V1\OrderExportExcelController;
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

Route::get('/', function () {
    return 'Copyright © ' . date('Y') . ' Watered Body Care';
});

Route::post('/stripe-webhook', WebhookOrderController::class);

Route::get('/complete-order', [OrderResponseController::class, 'complete']);
Route::get('/cancel-order/{order}', [OrderResponseController::class, 'cancel']);

Route::get('/images/agent-identity/{image_name}', [AgentIdentityPictureController::class, 'get']);

Route::get('/order/export-excel/{token}', [OrderExportExcelController::class, 'export']);

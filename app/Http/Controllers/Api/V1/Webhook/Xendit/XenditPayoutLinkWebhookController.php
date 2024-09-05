<?php

namespace App\Http\Controllers\Api\V1\Webhook\Xendit;

use App\Http\Controllers\Controller;
use App\Models\OrderRefund;
use App\Services\Email\OrderRefundEmailService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class XenditPayoutLinkWebhookController extends Controller
{
    use JsonResponsesTrait;

    public function __invoke(Request $request)
    {
        $this->checkToken($request);

        $orderRefund = OrderRefund::where('xendit_payout_link_id', $request->id)->first();

        if (!$orderRefund) return $this->successResponseMessage('This payout link webhook not for bali boat.');

        if ($orderRefund->type == 'fast_boat') $order = $orderRefund->fastBoatOrder;

        if ($orderRefund->type == 'private_boat') $order = $orderRefund->fastBoatOrder;

        if ($orderRefund->type == 'activity') $order = $orderRefund->fastBoatOrder;

        $user = $order->user;

        $xenditPayoutLink = $orderRefund->xenditPayoutLink;
        $updateStatus     = strtolower($request->status);

        if ($updateStatus == 'completed') {
            (new OrderRefundEmailService)->success($order);

            $order->status = 'refunded';

            $xenditPayoutLink->claimed_at = date('Y-m-d H:i:s', strtotime($request->updated));
        }

        if ($updateStatus == 'failed') {
            $failureMessage = $this->generateFailedMessage($request->failure_code, $user);

            if (strtoupper($request->error_code) == 'INSUFFICIENT_BALANCE') {
                (new OrderRefundEmailService)->failedForAdmin($order);
            }

            $xenditPayoutLink->failure_reason = $failureMessage;

            (new OrderRefundEmailService)->failed($order, $failureMessage);

            $order->status = 'declined';
        }

        if (($updateStatus == 'voided') || ($updateStatus == 'expired')) {
            $order->status = 'declined';

            (new OrderRefundEmailService)->failed($order, $failureMessage);
        }

        DB::beginTransaction();
        try {
            $xenditPayoutLink->status               = $updateStatus;
            $xenditPayoutLink->bank_code            = $request->bank_code;
            $xenditPayoutLink->account_holder_name  = $request->account_holder_name;

            $xenditPayoutLink->save();

            $orderRefund->status = $updateStatus;

            $orderRefund->save();

            $order->save();

            DB::commit();

            return $this->successResponse();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    protected function checkToken($request) {
        $callbackToken = $request->header('x-callback-token') ? $request->header('x-callback-token') : '';

        if (env('XENDIT_WEBHOOK_TOKEN') != $callbackToken) {
            $this->errorNotFound();

            return abort(404, 'Not found.');
        }
    }

    protected function generateFailedMessage($errorCode, $language)
    {
        if ($errorCode == 'API_VALIDATION_ERROR') {
            $message = 'Inputs are failing validation.';

            if ($language == 'id') $message = 'Validasi input gagal.';

            if ($language == 'de') $message = 'Die Eingaben können nicht validiert werden.';

            if ($language == 'ja') $message = '入力が検証に失敗しています。';

            if ($language == 'zh') $message = '输入验证失败。';
        } elseif ($errorCode == 'UNAUTHORIZED_MERCHANT_ERROR') {
            $message = 'This merchant is not authorized to perform this request.';

            if ($language == 'id') $message = 'Penjual ini tidak berwenang untuk melakukan permintaan ini.';

            if ($language == 'de') $message = 'Dieser Händler ist nicht berechtigt, diese Anfrage auszuführen.';

            if ($language == 'ja') $message = 'この販売者にはこのリクエストを実行する権限がありません。';

            if ($language == 'zh') $message = '该商家无权执行此请求。';
        } else {
            $message = 'Error in system.';

            if ($language == 'id') $message = 'Sistem Error.';

            if ($language == 'de') $message = 'Fehler im System.';

            if ($language == 'ja') $message = 'システムエラー。';

            if ($language == 'zh') $message = '系统错误。';
        }

        return $message;
    }
}

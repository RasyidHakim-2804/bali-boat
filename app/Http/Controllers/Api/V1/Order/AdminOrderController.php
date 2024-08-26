<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Exports\OrderExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\DeclineOrderRequest;
use App\Http\Requests\Api\Order\ExportOrderRequest;
use App\Http\Requests\Api\Order\SendOrderPaymentLinkRequest;
use App\Http\Resources\Order\AdminListOrderResource;
use App\Http\Resources\Order\AdminOrderResource;
use App\Mail\Order\OrderComfirmedNotificationMail;
use App\Mail\Order\OrderDeclineNotificationMail;
use App\Mail\Order\OrderPaymentDIrectlyNotificationMail;
use App\Mail\Order\OrderPaymentNotificationMail;
use App\Models\Order;
use App\Models\OrderExportTokens;
use App\Services\Admin\AdminPermissionService;
use App\Services\Payment\PaymentService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class AdminOrderController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = (new AdminPermissionService('order'));
    }

    public function index()
    {
        $this->permissionService->check('order');

        return AdminListOrderResource::collection(
            QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::scope('from_date'),
                AllowedFilter::scope('to_date'),
                AllowedFilter::scope('search'),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('user_type'),
                AllowedFilter::scope('transaction_type'),
            ])
            ->allowedSorts('id')
            ->with('details')
            ->orderBy('id', 'DESC')
            ->paginate(25)
        );
    }

    public function show(Order $order)
    {
        $this->permissionService->check('order');

        $order = new AdminOrderResource(Order::where('id', $order->id)->with('details', 'additionals', 'forms', 'payment')->first());

        return $this->successGet($order);
    }

    public function destroy(Order $order)
    {
        $this->permissionService->check('order');

        $order->delete();

        return $this->successStore();
    }

    public function confirm(Order $order)
    {
        $this->permissionService->check('order');

        DB::beginTransaction();
        try {
            if ($order->status != 'waiting_for_confirmation') return $this->errorValidationResponse('order_id', 'Order is unpaid. Please complete the payment.');

            $order->status = 'done';

            $order->save();

            $this->sendEmailOrderConfirmed($order);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function sendPayment(SendOrderPaymentLinkRequest $request, Order $order)
    {
        $this->permissionService->check('full');

        if ($request->send == 1) {
            if (isset($order->payment)) return $this->errorMessage('The order already has payment.');

            if ($order->status != 'done') return $this->errorMessage('The order not confirmed yet.');
    
            if ($order->grand_total_purchased == 0) return $this->errorMessage('The order is free.');
    
            $payment = (new PaymentService)->prepare()->pay($order);
    
            $order->status = 'waiting_for_payment';
    
            $order->save();
    
            $this->sendEmailPayment($order,$payment['invoice_url']);
        } else {
            $this->sendEmailPaymentDirectly($order);
        }

        return $this->successStore();
    }

    public function decline(DeclineOrderRequest $request, Order $order)
    {
        $this->permissionService->check('full');

        DB::beginTransaction();
        try {
            if ($order->status != 'waiting_for_confirmation') return $this->errorValidationResponse('order', 'Order is invalid. The status does not fulfill a requirement.');

            $order->status          = 'declined';
            $order->decline_reason  = $request->reason;

            $order->save();

            $this->sendEmailOrderDecline($order, $request);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->errorServer($e->getMessage());
        }

    }

    public function exportExcel(ExportOrderRequest $request)
    {
        $startDate = strtotime($request->start_date);
        $endDate   = strtotime($request->end_date);
        $interval  = abs($endDate - $startDate);
        $years     = floor($interval / (365*60*60*24));
        $months    = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));
        $days      = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        // if ((($days > 0) && ($months > 0)) || ($months > 1)) return $this->errorValidationResponse('end_date', 'Maximum range of day is 30 days.');

        $token = OrderExportTokens::where('token', $request->token)->first();

        if ($token->created_at <= date('Y-m-d H:i:s', strtotime('-3 minutes'))) {
            $token->delete();

            $this->errorValidationResponse('token', 'The token is expired.');
        }

        // $orderExportExcelToken              = new OrderExportTokens();
        // $orderExportExcelToken->token       = $this->generateToken();
        // $orderExportExcelToken->start_date  = $request->start_date;
        // $orderExportExcelToken->end_date    = $request->end_date;

        // $orderExportExcelToken->save();

        $data = Order::where('created_at', '>=', $request->start_date)->where('created_at', '<=', $request->end_date)->with('details', 'additionals')->get();

        return Excel::download(new OrderExcelExport($data, $request), 'report-orders-' . date("d-m-Y", strtotime($request->start_date)) . '-' . date("d-m-Y", strtotime($request->end_date)) . '.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'text/xlsx'
        ]);
    }

    public function generateExportToken()
    {
        $orderExportExcelToken              = new OrderExportTokens();
        $orderExportExcelToken->token       = $this->generateToken();
        $orderExportExcelToken->start_date  = '2024-01-01';
        $orderExportExcelToken->end_date    = '2024-01-01';

        $orderExportExcelToken->save();

        $data = [
            'token' => $orderExportExcelToken->token
        ];

        return $this->successGet($data);
    }

    protected function sendEmailOrderDecline($order, $request)
    {
        $user = $order->user;

        $data                   = [];
        $data['language']       = $user->language;
        $data['first_name']     = $user->first_name;
        $data['reason']         = $request->reason;

        Mail::to($user->email)->send((new OrderDeclineNotificationMail($data)));
    }

    protected function sendEmailOrderConfirmed($order)
    {
        $user = $order->user;

        $data                   = [];
        $data['language']       = $user->language;
        $data['first_name']     = $user->first_name;
        $data['order_url']      = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        
        Mail::to($user->email)->send((new OrderComfirmedNotificationMail($data)));
    }
    
    protected function generateToken()
    {
        $sameUuid = 1;

        while ($sameUuid > 0) {
            $token     = Str::random(100);
            $sameUuid  = OrderExportTokens::where('token', $token)->count();
        }
        
        return $token;
    }

    protected function sendEmailPayment($order, $payment_url)
    {
        $user = $order->user;
        
        $data                   = [];
        $data['language']       = $user->language;
        $data['first_name']     = $user->first_name;
        $data['payment_url']    = $payment_url;
        $data['order_url']      = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        
        Mail::to($user->email)->send((new OrderPaymentNotificationMail($data)));
    }

    protected function sendEmailPaymentDirectly($order)
    {
        $user = $order->user;
        
        $data                   = [];
        $data['language']       = $user->language;
        $data['first_name']     = $user->first_name;
        
        Mail::to($user->email)->send((new OrderPaymentDIrectlyNotificationMail($data)));
    }
}

<?php

namespace App\Http\Controllers\Web\V1;

use App\Exports\OrderExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderExportTokens;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrderExportExcelController extends Controller
{
    use JsonResponsesTrait;

    public function export(OrderExportTokens $token)
    {
        $data = Order::where('created_at', '>=', $token->start_date)->where('created_at', '<=', $token->end_date)->with('details', 'additionals')->get();

        return Excel::download(new OrderExcelExport($data), 'report-orders.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'text/xlsx'
        ]);
    }
}
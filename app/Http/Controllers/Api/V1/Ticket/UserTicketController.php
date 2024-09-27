<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Http\Controllers\Controller;
use App\Models\FastBoatOrder;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserTicketController extends Controller
{
    public function pdf(FastBoatOrder $fastBoatOrder)
    {
        $fastBoatOrder = $fastBoatOrder->load('details', 'payment', 'forms');

        $data = [
            'fastBoatOrder' => $this->generateTicketData($fastBoatOrder)
        ];

        return Pdf::loadView('pdf.ticket', $data)->stream('ticket.pdf');
    }

    // private function

    private function generateTicketData($fastBoatOrder)
    {
        $fastBoatOrder->details         = $fastBoatOrder->details->unique('schedule_id');
        $fastBoatOrder->forms           = $fastBoatOrder->forms->groupBy('schedule_id')->first();
        $fastBoatOrder->activity_date   = Carbon::createFromFormat('Y-m-d', $fastBoatOrder->activity_date)->format('l, d F Y');
        $fastBoatOrder->return_date     = Carbon::createFromFormat('Y-m-d', $fastBoatOrder->return_date)->format('l, d F Y');

        return $fastBoatOrder;
    }
}

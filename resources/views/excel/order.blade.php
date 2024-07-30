<table style="table-layout: fixed;width: 100%;  ">
    <thead>
        <h3>
            <tr>
                <th style="text-align:center;font-size:16px;">
                    Surya Bintang Adventures Orders
                </th>
            </tr>
            <tr><th style="text-align:center;font-size:16px;">{{ $start_date }} - {{ $end_date }}</th></tr>
        </h3>
    </thead>
    <thead>
        <tr></tr>
        <tr role="row">
            <th scope="col" rowspan="1">No</th>
            <th scope="col" rowspan="1">Order Number</th>
            <th scope="col" rowspan="1">Order Item</th>
            <th scope="col" rowspan="1">Order Additional</th>
            <th scope="col" rowspan="1">PIC Name</th>
            <th scope="col" rowspan="1">PIC Email</th>
            <th scope="col" rowspan="1">PIC Number</th>
            <th scope="col" rowspan="1">Activity Date</th>
            <th scope="col" rowspan="1">Arrival Time</th>
            <th scope="col" rowspan="1">Paid in System</th>
            <!-- <th scope="col" rowspan="1">Paid by Guest</th> -->
            <th scope="col" rowspan="1">Total Price</th>
        </tr>
    </thead>
    <tbody id='table-body'>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $order->uuid }}</td>
                <td>
                    <ul>
                    @foreach ($order->details as $detail)
                        <li>{{ $loop->index + 1 . '. ' . $detail->name . ' (' . $detail->quantity . 'pcs)' . ' - Rp ' . $detail->total_price }}</li>
                    @endforeach
                    </ul>
                </td>
                <td>
                    <ul>
                    @foreach ($order->additionals as $additional)
                        <li>{{ $loop->index + 1 . '. ' . $additional->name . ' (' . $additional->quantity . ' pcs)' . ' - Rp ' . $additional->total_price }}</li>
                    @endforeach
                    </ul>
                </td>
                <td>{{ $order->forms()->where('is_pic', 1)->first()->name }}</td>
                <td>{{ $order->forms()->where('is_pic', 1)->first()->email }}</td>
                <td>{{ $order->forms()->where('is_pic', 1)->first()->phone }}</td>
                <td>{{ $order->activity_date }}</td>
                <td>{{ $order->arrival_time }}</td>
                <td>{{ ($order->is_pay_in_system == 1) ? 'yes' : 'no' }}</td>
                <!-- <td>{{ ($order->is_pay_by_guest == 1) ? 'yes' : 'no' }}</td> -->
                <td>{{ $order->grand_total_purchased }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


    

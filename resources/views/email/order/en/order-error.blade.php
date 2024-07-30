@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">Error Order Detected</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
        Error order info :<br>
        - order uuid : "{{ $data['order_uuid'] }}"<br>
        - checkout session id : "{{ $data['checkout_id'] }}"<br>
        - checkout session status : "{{ $data['checkout_status'] }}"<br>
        - payment intent id : "{{ $data['payment_intent_id'] }}"<br>
        - payment intent status : "{{ $data['payment_intent_status'] }}"<br>
        <br>Please contact the developer to solve this problem. Thankyou.
    </p>
    </td>
</tr>
@endsection

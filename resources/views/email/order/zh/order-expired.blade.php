@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">你好 {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
    您的订单付款已过期，订单状态已更改为失败。单击“订单详细信息”按钮可查看您的订单信息。谢谢。
    </p>

    <!-- <div?><a href="{{ $data['order_url'] }}" style="display:inline-block; text-align: center; width:250px;background:#EFEFEF;color:#000000;border-radius: 100px;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:100px;" target="_blank"> Order Detail </a></div>
    <br> -->
    <a href="{{ $data['order_url'] }}" style="display:inline-block; text-align: center; width:250px;background:#EFEFEF;color:#000000;border-radius: 100px;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:100px;" target="_blank"> 订单详情 </a>
    
    </td>
</tr>
@endsection

@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">你好 {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
        您的订单已成功创建。我们的团队将确认或拒绝您的订单，并向您发送有关后续信息的电子邮件。谢谢。
    </p>

    <!-- <div?><a href="{{ $data['order_url'] }}" style="display:inline-block; text-align: center; width:250px;background:#EFEFEF;color:#000000;border-radius: 100px;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:100px;" target="_blank"> Order Detail </a></div>
    <br> -->
    <a href="{{ $data['payment_url'] }}" style="display:inline-block; text-align: center; width:250px;background:#EFEFEF;color:#000000;border-radius: 100px;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:100px;" target="_blank"> 支付 </a>
    
    </td>
</tr>
@endsection

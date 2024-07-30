@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">Hi {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
        Your email changes request for admin account to this email is success. For the next email changes will be available after 1 month from last email changes. Thankyou.
    </p>
    </td>
</tr>
@endsection

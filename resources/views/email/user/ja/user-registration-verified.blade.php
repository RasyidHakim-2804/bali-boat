@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">こんにちは {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
    当社の e コマース プラットフォームへの皆様のお越しを心より歓迎いたします。ログイン ボタンをクリックすると、あと一歩で最初の注文を作成できます。
    </p>

    <a href="{{ $data['login_page_url'] }}" style="display:inline-block; text-align: center; width:250px;background:#EFEFEF;color:#000000;border-radius: 100px;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:100px;" target="_blank"> ログイン </a>
    
    </td>
</tr>
@endsection

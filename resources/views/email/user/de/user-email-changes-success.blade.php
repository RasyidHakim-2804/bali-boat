@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">Hallo {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
        Ihre E-Mail-Änderungsanfrage an diese E-Mail war erfolgreich. Die nächsten E-Mail-Änderungen sind 1 Monat nach der letzten E-Mail-Änderung verfügbar. Danke schön.
    </p>
    </td>
</tr>
@endsection

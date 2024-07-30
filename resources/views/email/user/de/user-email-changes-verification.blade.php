@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">Hallo {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
    Für Ihr Konto liegt eine E-Mail-Änderungsanfrage für diese E-Mail vor. Bitte überprüfen Sie diese E-Mail, indem Sie auf die Schaltfläche "Verifizieren" klicken, um die E-Mail-Änderungen erfolgreich durchzuführen.
    </p>

    <a href="{{ $data['email_changes_verification_url'] }}" style="display:inline-block; text-align: center; width:250px;background:#EFEFEF;color:#000000;border-radius: 100px;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:100px;" target="_blank"> Verifizieren </a>
    
    </td>
</tr>
@endsection

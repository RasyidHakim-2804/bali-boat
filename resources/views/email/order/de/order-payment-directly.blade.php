@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">Hallo {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
        Ihre Bestellung ist bereits abgeschlossen, die Zahlung erfolgt vor Ort oder manuell. Bitte kontaktieren Sie uns für weitere Informationen oder Unterstützung. Danke schön.
    </p>
    </td>
</tr>
@endsection

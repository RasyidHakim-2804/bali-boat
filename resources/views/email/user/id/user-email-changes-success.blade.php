@extends('email.layout')

@section('content')
<tr>
    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:20px;font-weight:bold;line-height:1;text-align:left;color:#212b35;">Hi {{ $data['first_name'] }},</div>
    <p style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px; line-height: 25px;text-align:left;color:#212b35;">
    Permintaan anda untuk mengganti email akun anda ke email ini berhasil. Untuk permintaan pegrantian email berikutnya dapat dilakukan setelah 1 bulan. Terimakasih.
    </p>
    </td>
</tr>
@endsection

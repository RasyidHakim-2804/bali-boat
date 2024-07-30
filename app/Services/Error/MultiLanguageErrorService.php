<?php

namespace App\Services\Error;

class MultiLanguageErrorService
{
    public function generate($field, $error, $type = 'non_array', $numberParameter = 0) {

        $language   = config('app.locale');
        $field      = strtolower($field);

        if ($language == 'en') $message = $this->english($numberParameter)[$error];

        if ($language == 'id') {
            $message = $this->indonesian($numberParameter)[$error];
            $field   = $this->indonesianField()[$field];
        }

        if ($type == 'array') $message = ' :position' . $message;

        return ucfirst($field) . $message;
    }

    protected function english($numberParameter){
        return [
            'required'      => ' is required.',
            'invalid'       => ' is invalid.',
            'duplicated'    => ' is duplicated.',
            'integer'       => ' must be an integer.',
            'gt'            => ' must have more than ' . $numberParameter . '.',
            'min'           => ' must have at least ' . $numberParameter . '.',
            'numeric'       => ' must be an number.',
            'format'        => ' format is invalid.',
            'in_list'       => ' already in list.',
            'unique'        => ' already exists.',
            'digits'        => ' must have ' . $numberParameter . ' digits.',
            'same'          => ' must be same.',
            'image'         => ' must be an image.',
        ];
    }

    protected function indonesianField(){
        return [
            'product'           => 'produk',
            'variant'           => 'varian',
            'product variant'   => 'varian produk',
            'product at form'   => 'produk pada form',
            'product variant at form'   => 'varian produk pada form',
            'product quantity'  => 'jumlah produk',
            'quantity'          => 'jumlah',
            'description'       => 'deskripsi',
            'id description'    => 'deskripsi bahasa',
            'price'             => 'harga',
            'usd_price'         => 'harga usd',
            'is saleable'       => 'status untuk dijual',
            'email'             => 'email',
            'phone'             => 'telepon',
            'street1'           => 'alamat 1',
            'street2'           => 'alamat 2',
            'city'              => 'kota/kabupaten',
            'state'             => 'provensi',
            'country'           => 'negara',
            'country'           => 'negara',
            'zip'               => 'kode pos',
            'profile_picture'   => 'foto profile',
            'identity_picture'  => 'foto identitas',
            'recipient name'    => 'nama penerima',
            'first name'        => 'nama depan',
            'last name'         => 'nama belakang',
            'password'          => 'kata sandi',
            'confirm password'  => 'ulangi kata sandi',
            'old password'      => 'password saat ini',
            'pin'               => 'pin',
            'amount'            => 'jumlah',
            'channel type'              => 'tipe transaksi',
            'channel code'              => 'code transaksi',
            'channel holder_name'       => 'atas nama',
            'channel account_number'    => 'nomor rekening',
            'is pic'            => 'status pic',
            'age'               => 'umur',
            'form id'           => 'id form',
            'image'             => 'gambar',
            'short description'     => 'deskripsi singkat',
            'id short description'  => 'deskripsi singkat bahasa indonesia',
            'full description'      => 'deskripsi panjang',
            'id full description'   => 'deskripsi panjang bahasa indonesia',
            'message'               => 'pesan',
            'month'                 => 'bulan',
            'year'                  => 'tahun',
            'date'                  => 'tanggal',
            'time'                  => 'waktu',
            'arrival time'          => 'waktu kedatangan',
            'activity date'         => 'tanggal aktivitas',
            'is by cart'            => 'status order dari cart',
            'is pay in system'      => 'statu sistem pembayaran',
            'type'                  => 'tipe',
            'code'                  => 'kode',
            'additional'            => 'tambahan',
            'name'                  => 'nama',
        ];
    }

    protected function indonesian($numberParameter){
        return [
            'required'      => ' wajib diisi.',
            'invalid'       => ' tidak valid.',
            'duplicated'    => ' terduplikasi.',
            'integer'       => ' wajib berupa integer.',
            'gt'            => ' wajib bernilai lebih dari ' . $numberParameter . '.',
            'min'           => ' wajib minimal bernilai ' . $numberParameter . '.',
            'numeric'       => ' wajib berupa angka.',
            'format'        => ' tidak sesuai format.',
            'in_list'       => ' sudah terdaftar.',
            'unique'        => ' sudah terdaftar.',
            'digits'        => ' jumlah digit wajib ' . $numberParameter . '.',
            'same'          => ' wajib sama.',
            'image'         => ' wajib berupa gambar.',
        ];
    }
}
<?php

return [
    /*
    |---------------------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |---------------------------------------------------------------------------------------
    |
    | Baris bahasa berikut ini berisi standar pesan kesalahan yang digunakan oleh
    | kelas validasi. Beberapa aturan mempunyai banyak versi seperti aturan 'size'.
    | Jangan ragu untuk mengoptimalkan setiap pesan yang ada di sini.
    |
    */

    'accepted'        => ':attribute harus diterima.',
    'active_url'      => ':attribute bukan URL yang valid.',
    'after'           => ':attribute harus berisi tanggal setelah :date.',
    'after_or_equal'  => ':attribute harus berisi tanggal setelah atau sama dengan :date.',
    'alpha'           => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'      => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num'       => ':attribute hanya boleh berisi huruf dan angka.',
    'array'           => ':attribute harus berisi sebuah array.',
    'before'          => ':attribute harus berisi tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berisi tanggal sebelum atau sama dengan :date.',
    'between'         => [
        'numeric' => ':attribute harus bernilai antara :min sampai :max.',
        'file'    => ':attribute harus berukuran antara :min sampai :max kilobita.',
        'string'  => ':attribute harus berisi antara :min sampai :max karakter.',
        'array'   => ':attribute harus memiliki :min sampai :max anggota.',
    ],
    'boolean'        => ':attribute harus bernilai true atau false',
    'confirmed'      => 'Konfirmasi :attribute tidak cocok.',
    'date'           => ':attribute bukan tanggal yang valid.',
    'date_equals'    => ':attribute harus berisi tanggal yang sama dengan :date.',
    'date_format'    => ':attribute tidak cocok dengan format :format.',
    'different'      => ':attribute dan :other harus berbeda.',
    'digits'         => ':attribute harus terdiri dari :digits angka.',
    'digits_between' => ':attribute harus terdiri dari :min sampai :max angka.',
    'dimensions'     => ':attribute tidak memiliki dimensi gambar yang valid.',
    'distinct'       => ':attribute memiliki nilai yang duplikat.',
    'email'          => ':attribute harus berupa alamat surel yang valid.',
    'ends_with'      => ':attribute harus diakhiri salah satu dari berikut: :values',
    'exists'         => ':attribute yang dipilih tidak valid.',
    'file'           => ':attribute harus berupa sebuah berkas.',
    'filled'         => ':attribute harus memiliki nilai.',
    'gt'             => [
        'numeric' => ':attribute harus bernilai lebih besar dari :value.',
        'file'    => ':attribute harus berukuran lebih besar dari :value kilobita.',
        'string'  => ':attribute harus berisi lebih besar dari :value karakter.',
        'array'   => ':attribute harus memiliki lebih dari :value anggota.',
    ],
    'gte' => [
        'numeric' => ':attribute harus bernilai lebih besar dari atau sama dengan :value.',
        'file'    => ':attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'string'  => ':attribute harus berisi lebih besar dari atau sama dengan :value karakter.',
        'array'   => ':attribute harus terdiri dari :value anggota atau lebih.',
    ],
    'image'    => ':attribute harus berupa gambar.',
    'in'       => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ada di dalam :other.',
    'integer'  => ':attribute harus berupa bilangan bulat.',
    'ip'       => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'     => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'     => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'     => ':attribute harus berupa JSON string yang valid.',
    'lt'       => [
        'numeric' => ':attribute harus bernilai kurang dari :value.',
        'file'    => ':attribute harus berukuran kurang dari :value kilobita.',
        'string'  => ':attribute harus berisi kurang dari :value karakter.',
        'array'   => ':attribute harus memiliki kurang dari :value anggota.',
    ],
    'lte' => [
        'numeric' => ':attribute harus bernilai kurang dari atau sama dengan :value.',
        'file'    => ':attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'string'  => ':attribute harus berisi kurang dari atau sama dengan :value karakter.',
        'array'   => ':attribute harus tidak lebih dari :value anggota.',
    ],
    'max' => [
        'numeric' => ':attribute maskimal bernilai :max.',
        'file'    => ':attribute maksimal berukuran :max kilobita.',
        'string'  => ':attribute maskimal berisi :max karakter.',
        'array'   => ':attribute maksimal terdiri dari :max anggota.',
    ],
    'mimes'     => ':attribute harus berupa berkas berjenis: :values.',
    'mimetypes' => ':attribute harus berupa berkas berjenis: :values.',
    'min'       => [
        'numeric' => ':attribute minimal bernilai :min.',
        'file'    => ':attribute minimal berukuran :min kilobita.',
        'string'  => ':attribute minimal berisi :min karakter.',
        'array'   => ':attribute minimal terdiri dari :min anggota.',
    ],
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'password'             => 'Kata sandi salah.',
    'present'              => ':attribute wajib ada.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':attribute wajib diisi.',
    'required_if'          => ':attribute wajib diisi bila :other adalah :value.',
    'required_unless'      => ':attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => ':attribute wajib diisi bila terdapat :values.',
    'required_with_all'    => ':attribute wajib diisi bila terdapat :values.',
    'required_without'     => ':attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => ':attribute wajib diisi bila sama sekali tidak terdapat :values.',
    'same'                 => ':attribute dan :other harus sama.',
    'size'                 => [
        'numeric' => ':attribute harus berukuran :size.',
        'file'    => ':attribute harus berukuran :size kilobyte.',
        'string'  => ':attribute harus berukuran :size karakter.',
        'array'   => ':attribute harus mengandung :size anggota.',
    ],
    'starts_with' => ':attribute harus diawali salah satu dari berikut: :values',
    'string'      => ':attribute harus berupa string.',
    'timezone'    => ':attribute harus berisi zona waktu yang valid.',
    'unique'      => ':attribute sudah ada sebelumnya.',
    'uploaded'    => ':attribute gagal diunggah.',
    'url'         => 'Format :attribute tidak valid.',
    'uuid'        => ':attribute harus merupakan UUID yang valid.',

    /*
    |---------------------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi Kustom
    |---------------------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan pesan validasi untuk atribut sesuai keinginan dengan menggunakan 
    | konvensi "attribute.rule" dalam penamaan barisnya. Hal ini mempercepat dalam menentukan
    | baris bahasa kustom yang spesifik untuk aturan atribut yang diberikan.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |---------------------------------------------------------------------------------------
    | Kustom Validasi Atribut
    |---------------------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar 'placeholder' atribut dengan sesuatu yang
    | lebih mudah dimengerti oleh pembaca seperti "Alamat Surel" daripada "surel" saja.
    | Hal ini membantu kita dalam membuat pesan menjadi lebih ekspresif.
    |
    */

    'attributes' => [
        'products.*.id'     => 'Produk :position',
        'forms.*.product_id'=> 'Produk pada formulir :position',
        'products.*'        => 'Produk :position',
        'product_id'        => 'Produk',
        'product'           => 'Produk',
        'products'          => 'Produk',

        'products.*.variant_id' => 'Varian produk :position',
        'forms.*.variant_id'    => 'Varian produk pada formulir :position',
        'variant_id'            => 'Varian produk',
        'variants'              => 'Varian produk',
        'variant'               => 'Varian produk',

        'products.*.quantity'   => 'Jumlah produk :position',
        'quantity'              => 'Jumlah',

        'forms.*.name'      => 'Nama pada formulir :position',
        'forms.*.age'       => 'Umur pada formulir :position',
        'forms.*.email'     => 'email pada formulir :position',
        'forms.*.phone'     => 'Telepon pada formulir :position',

        'additionals.*.id'          => 'additional :position',
        'additionals.*.quantity'    => 'jumlah additional :position',

        'product_variants.*.name'           => 'Nama varian :position',
        'product_variants.*.description'    => 'Deskripsi varian :position',
        'product_variants.*.id_description' => 'Deskripsi bahasa varian :position',
        'product_variants.*.price'          => 'Harga varian :position',
        'product_variants.*.usd_price'      => 'Harga usd varian :position',
        'product_variants.*.nett_price'     => 'Harga nett varian :position',
        'product_variants.*.max_order'      => 'Maximum order varian :position',
        'product_variants.*.min_order'      => 'Minimum order varian :position',
        'product_variants.*.person_qty'     => 'Jumlah orang varian :position',
        'product_variants.*.product_additionals.*.additional_id'    => 'Additional :position varian :position',

        'product_additionals.*.additional_id' => 'Additional produk :position',

        'description'       => 'Deskripsi',
        'id_description'    => 'Deskripsi bahasa',
        'price'             => 'Harga',
        'usd_price'         => 'Harga usd',
        'nett_price'        => 'Harga nett',
        'is_saleable'       => 'Status dijual',
        'email'             => 'Email',
        'phone'             => 'Telepon',
        'street1'           => 'Alamat 1',
        'street2'           => 'Alamat 2',
        'city'              => 'Kota/kabupaten',
        'state'             => 'Provensi',
        'country'           => 'Negara',
        'zip'               => 'Kode pos',
        'profile_picture'   => 'Foto profile',
        'identity_picture'  => 'Foto identitas',
        'recipient_name'    => 'Nama penerima',
        'first_name'        => 'Nama depan',
        'last_name'         => 'Nama belakang',
        'password'          => 'Kata sandi',
        'confirm_password'  => 'Konfirmasi kata sandi',
        'old_password'      => 'Password saat ini',
        'pin'               => 'Pin',
        'amount'            => 'Jumlah',
        'channel_type'              => 'Tipe transaksi',
        'channel_code'              => 'Kode transaksi',
        'channel_holder_name'       => 'Atas nama rekening',
        'channel_account_number'    => 'Nomor rekening',
        'is_pic'            => 'Status pic',
        'age'               => 'Umur',
        'form_id'           => 'Id formulir',
        'image'             => 'Gambar',
        'short_description'     => 'Deskripsi singkat',
        'id_short_description'  => 'Deskripsi singkat bahasa indonesia',
        'full_description'      => 'Deskripsi panjang',
        'id_full_description'   => 'Deskripsi panjang bahasa indonesia',
        'message'               => 'Pesan',
        'month'                 => 'Bulan',
        'year'                  => 'Tahun',
        'date'                  => 'Tanggal',
        'time'                  => 'Waktu',
        'arrival_time'          => 'Waktu kedatangan',
        'activity_date'         => 'Tanggal aktivitas',
        'is_by_cart'            => 'Status order dari cart',
        'is_pay_in_system'      => 'Statu sistem pembayaran',
        'type'                  => 'Tipe',
        'code'                  => 'Kode',
        'additional'            => 'Tambahan',
        'name'                  => 'Nama',
    ],
];
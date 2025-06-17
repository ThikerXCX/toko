<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .nota {
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table tr:nth-child(even) {
            background-color: #f3f4f6; /* Stripe warna abu-abu muda */
        }
        .table th, .table td {
            padding: 4px 0;
            text-align: left;
            font-size: 10px;
        }

        .total {
            font-weight: bold;
            border-top: 1px dashed #000;
            margin-top: 8px;
            padding-top: 5px;
        }

        .logo {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>
    <div class="nota">
        <div class="center">
            {{-- Tambahkan logo jika ingin --}}
            <img style="" src="{{ public_path('logo.jpg') }}" class="logo" alt="Logo">
            <div class="title">TOKO CAHAYA TIMUR 99</div>
            <p>Jl. Tanjung Raya 2, Kelurahan Parit Mayor,Pontianak<br>Telp: 085853551266</p>
            <p>Kasir: {{ $penjualan->user->name }}</p>
            <p>Tanggal: {{ $penjualan->tanggal }}</p>
        </div>

        <table class="table">
        
           <tbody>
             @foreach ($penjualan->details as $item)
                <tr>
                    <td colspan="2">{{ $item->product->name }}</td>
                </tr>
                <tr>
                    <td>{{ $item->qty }} x Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td align="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
           </tbody>
        </table>

        <p class="total">Total: Rp {{ number_format($penjualan->total, 0, ',', '.') }}</p>

        <div class="center">
            <p>Terima kasih telah berbelanja!</p>
        </div>
    </div>
</body>
</html>

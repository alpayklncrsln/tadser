<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//TR" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gün Sonu {{now()->format('d m Y')}}</title>
    <style>
        * {
            font-family: "DejaVu Sans Mono", monospace;
            margin: 0;
            padding: 0;
            font-size: 11px;
            box-sizing: border-box;
        }

        .page-break {
            page-break-after: always;
        }

        body {
            color: #333;
        }

        .invoice-container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .header .title {
            font-size: 24px;
            font-weight: bold;
        }

        .header .invoice-details {
            text-align: right;
        }

        .section {
            margin-top: 20px;
        }

        .section h2 {
            font-size: 18px;
            color: #f00;
            margin-bottom: 10px;
        }

        .section p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .totals table {
            width: auto;
            border: none;
        }

        .totals table th, .totals table td {
            border: none;
            text-align: right;
        }

        .totals .grand-total {
            font-size: 20px;
            color: #f00;
        }
    </style>
</head>
<body>
@forelse($orders as $order )
    <div class="invoice-container">
        <div class="header">
            <div class="title">Çaykur</div>
            <div class="invoice-details">
                <p><strong>Tarih:</strong>{{now()->format('d m Y H:i')}}</p>
            </div>
        </div>
        <div class="section">
            <h2>Müşteri Adı</h2>
            <p><strong>{{$order->customer->name??'-'}}</strong></p>
            <p>Müşteri Kodu: {{$order->customer->code??'-'}}</p>
            <p>İşletme Sahibi:{{$order->customer->owner??'-'}}</p>
        </div>
        <div class="section">
            <h2>Temsilci</h2>
            <p><strong>{{$order->user->name}}</strong></p>
        </div>
        <table>
            <thead>
            <tr>
                <th>Kodu</th>
                <th>Ürün Adı</th>
                <th>Birim</th>
                <th>Iskonto</th>
                <th>Fiyat</th>
            </tr>
            </thead>
            <tbody>
            @forelse($order->orderProducts as $product)
                <tr>
                    <td>{{$product->product->code}}</td>
                    <td>{{$product->product->name}}</td>
                    <td>{{$product->quantity}}
                        @if($product->quantity_type->value=='box')
                            Koli
                        @elseif($product->quantity_type->value=='quantity')
                            Adet
                        @else
                            -
                        @endif </td>
                    <td>%{{$product->discount??'-'}}</td>
                    <td>{{$product->price?'₺'.$product->price:'-'}}</td>
                </tr>
            @empty
                <tr>
                    <td columnspan="5">Ürün Yok</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@empty
@endforelse
</body>
</html>

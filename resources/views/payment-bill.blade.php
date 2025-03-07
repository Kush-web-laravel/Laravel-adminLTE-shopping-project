<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Invoice</h1>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Product Price</th>
            <th>Total Amount</th>
            <th>Payment Status</th>
        </tr>
            @php
                $totalAmount = 0;
            @endphp
        @foreach($payments as $payment)
            @php
                $totalAmount += $payment->total_amount;
            @endphp
            <tr>
                
                <td>{{ $payment->product->productName }}</td>
                <td>{{ $payment->quantity }}</td>
                <td>
                    @if($payment->currency == 'usd')
                        ${{ $payment->amount }}
                    @else
                        ₹{{ $payment->amount }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>  
                    @if($payment->currency == 'usd')
                        ${{ $totalAmount }}
                    @else
                        ₹{{ $totalAmount }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ $payment->payment_status }}</td>
            </tr>
        @endforeach
        
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice Email</title>
</head>
<body>
    
    <h1>Thank you for your purchase!</h1>
    <p>Dear Customer,</p>
    <p>We have received your payment for the following order:</p>
    <ul>
        @foreach($payments as $payment)
            <li>Product Name: {{ $payment->product->productName }}</li>
            <li>Quantity: {{ $payment->quantity }}</li>
            <li>Product Price: 
                @if($payment->currency == 'usd')
                    ${{ $payment->amount }}
                @else
                    ₹{{ $payment->amount }}
                @endif
            </li>
            <li>Total Amount: 
                @if($payment->currency == 'usd')
                    ${{ $payment->total_amount }}
                @else
                    ₹{{ $payment->total_amount }}
                @endif
            </li>
            <li>Payment Status: {{ $payment->payment_status }}</li>
        @endforeach
    </ul>
    <p>Please find the attached invoice for your reference.</p>
    <p>Thank you for shopping with us!</p>
</body>
</html>

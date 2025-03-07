<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body>
    <h2>Invoice Details</h2>
    <h4>Invoice Number : {{$invoice->invoice_number}}</h4>
    @php
        $grossTotal = 0;
        $lessTotal = 0;
        $netTotal = 0;
        $touch = 0;
        $finalTotal = 0;
    @endphp
    <table border="1" style="width: 100% ;border-collapse:collapse;">

        <thead>
            <tr>
                <th>Item name</th>
                <th>Gross weight</th>
                <th>Less weight</th>
                <th>Net weight</th>
                <th>Touch</th>
                <th>Final weight</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->item as $index => $item)
            <tr>
                <td>{{$item->item_name}}</td>
                <td>{{$item->gross_weight}}</td>
                <td>{{$item->less_weight}}</td>
                <td>{{$item->net_weight}}</td>
                <td>{{$item->touch}}</td>
                <td>{{$item->final_weight}}</td>
            </tr>
            @php
                $grossTotal += $item->gross_weight;
                $lessTotal += $item->less_weight;
                $netTotal +=  $item->net_weight;
                $touch += $item->touch;
                $finalTotal +=  $item->final_weight;
            @endphp
            @endforeach
            <tr>
                <td>Total : </td>
                <td>{{$grossTotal}}</td>
                <td>{{$lessTotal}}</span></td>
                <td>{{$netTotal}}</span></td>
                <td>{{$touch}}</span></td>
                <td colspan="2">{{$finalTotal}}</span></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
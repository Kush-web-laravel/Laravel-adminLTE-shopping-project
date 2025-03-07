@extends('admin.products.app')

@section('content')

    <main class="app-main">
        <h2 class="text-center mt-5">My Orders</h2>

        <div class="productsBtn">
            <a href="{{route('products.index')}}" class="btn btn-dark">Products Page</button></a>
        </div>
        <div class="card-body m-4">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <table id="myOrdersTable" class="display">
                <thead>
                    <tr>
                        <td>ID</th>
                        <td>Product Name</td>
                        <td>Product Price</td>
                        <td>Product Quantity</td>
                        <td>Total Amount</td>
                        <td>Order Status</td>
                        <td>Product Images</td>
                        <td>Actions</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                    @endphp
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $payment->product->productName }}</td>
                            <td>{{ $payment->amount }}</td>
                            <td>{{ $payment->quantity }}</td>
                            <td>{{ $payment->total_amount }}</td>
                            <td>
                                @if($payment->payment_status == 'complete')
                                        <span class="badge bg-warning text-black">{{ ucfirst($payment->payment_status) }}</span>
                                    @elseif($payment->payment_status == 'refunded')
                                            <span class="badge bg-info text-black">{{ ucfirst($payment->payment_status) }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($payment->payment_status) }}</span>
                                @endif
                            </td>
                            <td><img src="{{ asset('storage/'.$payment->product->productImage) }}" alt="Product image" width="50"/></td>
                            <td>
                                <div class="button-group">
                                    <a href="{{ route('product.buy', $payment->product_id) }}" class="btn btn-sm btn-secondary reorder-btn">Reorder</a>
                                    @if($payment->payment_status != 'refunded' && $payment->payment_status != 'cancelled')
                                        <form action="{{ route('products.refund', $payment->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <input type="hidden" name="stripe_payment_id" value="{{ $payment->stripe_payment_id }}">
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel Order</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

@endsection
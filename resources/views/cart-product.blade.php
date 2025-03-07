@extends('admin.products.app')

@section('content')

<main class="app-main">
    <h2 class="text-center mt-5">My Cart</h2>

    <div class="addBtn">
        <a href="{{ route('products.index') }}" class="btn btn-dark">Products Page</a>
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
        <form action="{{ route('product.cart.payment') }}" method="POST">
            @csrf
            <table id="cartTable" class="display text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Product Images</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                        $totalCost = 0
                    @endphp

                    @foreach($cartItems as $cartItem)
                   
                    @php
                        $totalCost = $totalCost + ($cartItem->product_price * $cartItem->product_quantity)
                    @endphp
                        <tr class="cart-item" id="cart-item-{{$cartItem->id}}">
                            <td>{{ $counter++ }}</td>
                            <td>{{ $cartItem->product_name }}</td>
                            <td>{{ $cartItem->product_price }}</td>
                            <td><img src="{{ asset('storage/'.$cartItem->product_image) }}" alt="Product Image" class="thumbnail-img" width="50" height="50"></td>
                            <td>
                                <input type="number" value="{{ $cartItem->product_quantity }}" class="quantity-input">
                                <br/><span class="quantityErr"></span>
                            </td>
                            <td>
                            <button type="button" class="btn btn-sm btn-danger remove-btn" data-id="{{ $cartItem->id }}">Remove</button>
                            </td>
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <input type="hidden" name="product_name" value="{{$cartItem->product_name}}">
                            <input type="hidden" name="product_price" value="{{ $cartItem->product_price }}">
                            <input type="hidden" name="product_quantity" value="{{ $cartItem->product_quantity }}">
                            <input type="hidden" name="product_id" value="{{ $cartItem->product_id }}">
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2">Total Cost</th>
                        <td><span id="totalCost">{{ $totalCost }}</span></td>
                        <td colspan="2">
                            <input type="text" name="discount" id="discount" placeholder="Enter GET20 code to get 20% off">
                            <br/><span id="discountErr"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="stripe_session_id" value="{{ session('stripe_session_id') }}">
            <button type="submit" class="btn btn-warning mt-3" id="cart-buy">Buy Now</button>
        </form>
    </div>
</main>

@endsection

@extends('admin.products.app')

@section('content')

    <main class="app-main">
            <h2 class="text-center mt-5">Buy Product</h2>

            @if (session('success'))
                <div
                    style="color: green;
                        border: 2px green solid;
                        text-align: center;
                        padding: 5px;margin-bottom: 10px;">
                    {{ session('success') }}
                </div>
            @endif

            <form id="buyProduct" class="mt-3" action="{{ route('product.payment') }}" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->id() }}" />
                <input type="hidden" name="product_id" value="{{$product->id}}" />
                <input type='hidden' name='stripeToken' id='stripe-token-id'>
                <div class="mb-3">
                    <label for="productName">Product Name:</label>
                    <input type="text" name="product_name" id="productName" value="{{ $product->productName }}" class="form-control" placeholder="Enter product name" readonly/>
                </div>

                <div class="mb-3">
                    <label for="productPrice">Product Price:</label>
                    <input type="text" name="price" id="productPrice" class="form-control" value="{{ $product->productPrice }}"placeholder="Enter product price" readonly/>
                </div>

                <div class="mb-3">
                    <label for="productImage">Product Image:</label><br/><br/>
                    <img src= "{{ asset('storage/'.$product->productImage) }}" alt="Product Image" width="200" style="color:white"/>
                </div>

                <div class="mb-3">
                    <label for="productQuantity">Product Quantity:</label>
                    <input type="number" name="quantity" id="productQuantity" class="form-control" placeholder="Enter product quantity"/>
                    <span id="quantityErr"></span>
                </div>

                <div class="d-grid">
                    <input type="submit" value="Pay &#8377; {{ $product->productPrice }}" id="payBtn" class="btn btn-warning"/>
                </div>
            </form>
    </main>
   
@endsection
@extends('admin.products.app')
@section('content')

<main class="app-main"> <!--begin::App Content Header-->
    <h2 class="text-center mt-5">Product list</h2>

    <div class="addBtn">
        <a href="{{route('products.add')}}" class="btn btn-success">Add Product</button></a>
        <a href="{{ route('products.myOrders') }}" class="btn btn-dark ms-2">My Orders</a>
        <a href="{{ route('cart.index') }}" class="btn btn-primary ms-2">My Cart</a>
    </div>
    <div class="card-body m-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <small id="deleteMsg" class="text-success"></small>
        <table id="productsTable" class="display">
            <thead>
                <tr>
                    <td>ID</th>
                    <td>Product Name</td>
                    <td>Product Price</td>
                    <td>Product Details</td>
                    <td>Product Images</td>
                    <td>Actions </td>
                </tr>
            </thead>
        </table>
    </div>
</main>
@endsection
@extends('admin.products.app')

@section('content')

    <main class="app-main">
        <h2 class="text-center mt-5">Add Products</h2>

        <form id="addProducts" class="mt-3">
            @csrf
            <div class="mb-3">
                <label for="productName">Product Name:</label>
                <input type="text" name="productName" id="productName" class="form-control" placeholder="Enter product name"/>
                <small id="nameErr" class="text-danger"></small>
            </div>

            <div class="mb-3">
                <label for="productPrice">Product Price:</label>
                <input type="number" name="productPrice" id="productPrice" class="form-control" placeholder="Enter product price"/>
                <small id="priceErr" class="text-danger"></small>
            </div>

            <div class="mb-3">
                <label for="productImage">Product Image:</label>
                <input type="file" name="productImage" id="productImage" class="form-control" accept=".jpeg, .png, .jpg, .gif"/>
                <small id="imageErr" class="text-danger"></small>
            </div>

            <div class="mb-3">
                <label for="productDetails">Product Description:</label>
                <textarea name="productDetails" id="productDetails" class="form-control" rows="4" placeholder="Enter product description"></textarea>
                <small id="detailsErr" class="text-danger"></small>
            </div>
            <div class="mb-3">
                <small id="formErr" class="text-danger"></small>
            </div>
            <div class="d-grid">
                <input type="button" id="submitBtn" value="Add product" class="btn btn-primary"/>
            </div>
        </form>
    </main>

@endsection
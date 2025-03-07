@extends('categories.edit-app')

@section('content')

<form id="editCategoryForm">
        @csrf
        <input type="hidden" id="category_id" value="{{ $category->id }}">
       
        <label for="categoryName">Category Name:</label>
        <input type="text" id="categoryName" name="category_name" value="{{ $category->name }}"><br/>
        <small id="cnameErr"></small>
        <br/><br/>

        <div class="sub_category">
            <h4>Subcategories</h4>
            @foreach($category->subcategories as $subcategory)
                <div class="subcategory-row" id="sub_row" data-id="{{ $subcategory->id }}">
                    <label for="sub_name_{{ $subcategory->id }}">Subcategory Name:</label>
                    <input type="text" name="sub_name[]" id="sub_name_{{ $subcategory->id }}" value="{{ $subcategory->name }}">
                    <small id="snameErr_{{ $subcategory->id }}"></small>
                   
                    <label for="sub_price_{{ $subcategory->id }}">Subcategory Price:</label>
                    <input type="number" name="sub_price[]" id="sub_price_{{ $subcategory->id }}" value="{{ $subcategory->price }}"><button type="button" id="addBtn">&#43;</button>
                    <small id="spriceErr_{{ $subcategory->id }}"></small>

                    <input type="hidden" name="sub_id[]" value="{{ $subcategory->id }}">
                    
                </div>
                <br/>
            @endforeach
        </div>

        <button type="submit" id="submitEditForm">Update Category</button>
    </form>

    @endsection
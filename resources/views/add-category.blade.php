@extends('categories.add-app')

@section('content')

    <div class="container">

        <span id="form-success"></span>
        <form id="categoryForm">
            @csrf
                <h3>Category</h3>
                <label for="cname">Name : </label>
                <input type="text" name="category_name" id="cname" placeholder="Enter category name" /><br/>
                <small id="cnameErr"></small><br/><br/>
            <div id="sub_category">

                <h3>Sub Category</h3>
                <div class="sub_row">
                    <label for="sname">Name : </label>
                    <input type="text" name="sub_name[]" class="sname" placeholder="Enter sub category name" /><br/>
                    <small class="suberror"></small><br/><br/>

                    <label for="sprice">Price :  </label>
                    <input type="number" name="price[]" class="sprice" placeholder="Enter sub category price" /><button type="button" id="addBtn">&#43;</button><br/>
                    <small class="suberror"></small><br/><br/>
                </div>
            </div>
            <button type="submit" id="submitBtn">Submit</button>
        </form>
    </div>
    <hr/>
    <table id="categoryTable">
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Subcategories</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        
        </tbody>
    </table>

@endsection
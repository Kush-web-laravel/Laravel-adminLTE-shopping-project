<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Category Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <link href="{{asset('css/category.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('toastr/toastr.min.css')}}" />
    <link rel="stylesheet" href="{{asset('sweetalert2/sweetalert2.min.css')}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="container">
        <medium id="answers"></medium>
        <form id="categoryForm">
            @csrf
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="category_name" autocomplete="off">
            <medium id="categoryNameErr"></medium>
            <div id="subcategories">
                
            </div>

            <br>
            <button type="submit" id="submitBtn">Submit</button>
        </form>
    </div>

    <input type="text" id="search" placeholder="Search categories...">
    <input type="number" id="min-price"  placeholder="Min Price">
    <input type="number" id="max-price"  placeholder="Max Price">

    <button class="btn btn-warning" id="download"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
  <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
</svg></button>

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
    <div id="pagination-links">
        
    </div>
    
    <script src="{{asset('toastr/toastr.min.js')}}"></script>
    <script src="{{asset('sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('html2canvas/html2canvas.min.js')}}"></script>
    <script src="{{asset('jspdf/jspdf.min.js')}}"></script>
    <script>
        
        $(document).ready(function(){
            let subcategoryIndex = 1;
            var page = 1;

            toastr.options = {
                "closeButton": true,
                "newestOnTop": true,
                "positionClass": "toast-top-right"
            };
            var subcategory = '<div class="subcategory"><label for="subCategoryName">Subcategory Name : </label><input type="text" name="sub_category_name[]_' + subcategoryIndex + '"class = "subCategoryName"><br/><label for="subCategoryPrice">Subcategory Price : </label><input type="number" name="sub_category_price[]_' + subcategoryIndex + '"class = "subCategoryPrice"><button type="button" id="addSubcategory">+</button></div>';
            $('#subcategories').append(subcategory);

            function appendSubCategory()
            {
                var addsubcategory = '<div class="subcategory"><label for="subCategoryName">Subcategory Name : </label><input type="text" name="sub_category_name[]_' + subcategoryIndex + '"class = "subCategoryName"><br/><label for="subCategoryPrice">Subcategory Price : </label><input type="number" name="sub_category_price[]_' + subcategoryIndex + '"class = "subCategoryPrice"><button type="button" class="removeSubcategory">x</button></div>';
                $('#subcategories').append(addsubcategory);
                if($('.subcategory').length >= 5){
                    $('#addSubcategory').hide();
                }
            }
            $('#addSubcategory').click(function(){
                subcategoryIndex++;
                appendSubCategory();
                console.log($('.subcategory').length);
                
            });
           
            $('#search').on('keyup', function() {
                let query = $(this).val();
                let minPrice = $('#min-price').val();
                let maxPrice = $('#max-price').val();
                searchCategories(query, minPrice, maxPrice);
            });

            // jQuery validation on form submission
            $('form#categoryForm').on('submit', function(e){
                e.preventDefault();
                var categoryName = $('#categoryName').val();
                if(categoryName == ''){
                    $('#categoryNameErr').addClass('error');
                    $('#categoryNameErr').text('Category name is required').css('font-weight','bold');
                }else{
                    $('#categoryNameErr').removeClass('error');
                    $('#categoryNameErr').text('');
                }
                $('.subCategoryName').each(function(){
                    $(this).rules("add", 
                    {
                        required: true,
                        messages: {
                            required: "Subcategory name is required",
                        }
                    });
               });

                $('.subCategoryPrice').each(function(){
                    $(this).rules("add",{
                        required: true,
                        number: true,
                        min: 1,
                        messages: {
                            required: "Subcategory price is required",
                            number: "Subcategory price must be a number",
                            min: "Subcategory price must be greater than 0",
                        }
                    });
               });
            });

            $('#categoryForm').validate(function(e){
                if(this.valid()){
                    console.log(this.valid())
                }
            });


            fetchCategories(page);

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                page = $(this).attr('data-page');
                fetchCategories(page);
            });

            function fetchCategories()
            {
                $.ajax({
                    url: "{{ route('categories.fetch') }}",
                    type: "GET",
                    data : {
                        page: page,
                    },
                    success : function(response){
                        if(response.status === 'success'){
                            let categories = response.categories;
                            let tableRows = '';

                            categories.forEach(function(category){
                                let subcategories = '';

                                category.subcategories.forEach(function(subcategory){
                                    subcategories += `<p>Name : ${subcategory.name}, Price: ${subcategory.price}</p>`;
                                });

                                tableRows +=  `
                                
                                <tr>
                                    <td>${category.name}</td>
                                    <td>${subcategories}</td>
                                    <td> 
                                        <button class="editBtn" data-id="${category.id}">Edit</button>
                                        <button class="deleteBtn" data-id="${category.id}">Delete</button>
                                    </td>
                                </tr>
                                `;
                            });

                            $('#categoryTable tbody').html(tableRows);

                            renderPagination(response.current_page, response.last_page);
                        }
                    }
                });
            } 

            function renderPagination(currentPage, lastPage) {
                let paginationLinks = '<nav><ul class="pagination">';

                for (let i = 1; i <= lastPage; i++) {
                    paginationLinks += `<li class="page-item ${i == currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                }

                paginationLinks += '</ul></nav>';
                $('#pagination-links').html(paginationLinks);
            }

            $(document).on('click', '.editBtn', function() {

                let categoryId = $(this).data('id');
                console.log(categoryId)

               
                $.ajax({
                    url: "{{route('categories.edit', ':id')}}".replace(':id', categoryId),
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.scroll({
                                top: 0, 
                                left: 0, 
                                behavior: 'smooth'
                            });
                            let category = response.category;
                            let subcategories = category.subcategories;

                            
                            $('#categoryName').val(category.name);

                           
                            $('#subcategories').empty();
                            
                            if($('#categoryId').val() != ''){
                                $('input[name="category_id"]').remove();
                                let appendCategoryId = `<input type="hidden" id="categoryId" name="category_id" value="${categoryId}">`;
                                $('.container #categoryForm').append(appendCategoryId);
                            }else{
                                let appendCategoryId = `<input type="hidden" id="categoryId" name="category_id" value="${categoryId}">`;
                                $('.container #categoryForm').append(appendCategoryId);
                            }
                            
                            
                            subcategories.forEach(function(subcategory, index) {

                                let subcategoryHtml = `
                                    <div class="subcategory">
                                        <input type="hidden" name="existing_subcategory_ids[]" value="${subcategory.id}">
                                        <label for="subCategoryName">Subcategory Name:</label>
                                        <input type="text" name="existing_sub_category_name[]" value="${subcategory.name}" class="subCategoryName">
                                        <label for="subCategoryPrice">Subcategory Price:</label>
                                        <input type="number" name="existing_sub_category_price[]" value="${subcategory.price}" class="subCategoryPrice">
                                        <button type="button" class="removeSubcategory" data-id="${subcategory.id}">&times;</button>
                                    </div>`;

                                $('#subcategories').append(subcategoryHtml);

                                // Check if it's the first subcategory
                                if (index === 0 && $('#subcategories .subcategory').length < 5) {
                                    // Remove the remove button from the first subcategory
                                    $('#subcategories .subcategory:first .removeSubcategory').remove();

                                    // Append the Add button to the first subcategory
                                    $('#subcategories .subcategory:first').append(`<button type="button" id="addSubcategory">&#43;</button>`);
                                }
                            });

                                // Attach event listener to dynamically added Add button
                                $(document).off('click', '#addSubcategory').on('click', '#addSubcategory', function() {
                                appendSubcategory();
                                checkSubcategoryLimit(); // Check subcategory limit each time a new one is added
                                });

                                function appendSubcategory() {
                                let subcategoryCount = $('#subcategories .subcategory').length;

                                // Append a new subcategory only if less than 5
                                if (subcategoryCount < 5) {
                                    let newSubcategoryHtml = `
                                        <div class="subcategory">
                                            <label for="subCategoryName">Subcategory Name:</label>
                                            <input type="text" name="sub_category_name[]" class="subCategoryName">
                                            <label for="subCategoryPrice">Subcategory Price:</label>
                                            <input type="number" name="sub_category_price[]" class="subCategoryPrice">
                                            <button type="button" class="removeSubcategory">&times;</button>
                                        </div>`;

                                    $('#subcategories').append(newSubcategoryHtml);
                                }

                                checkSubcategoryLimit();
                                }

                                // Check the subcategory count and hide the Add button if >= 5
                                function checkSubcategoryLimit() {
                                let subcategoryCount = $('#subcategories .subcategory').length;

                                if (subcategoryCount >= 5) {
                                    $('#addSubcategory').hide();
                                } else {
                                    $('#addSubcategory').show();
                                }
                                }

                                // Event listener for removing subcategories
                                $(document).on('click', '.removeSubcategory', function() {
                                $(this).closest('.subcategory').remove();
                                checkSubcategoryLimit(); // Check the limit again after removing
                                });
                        
                            // Show the submit button with 'Update' action
                            $('#submitBtn').text('Update');
                        }
                    }
                });
            });

            $('form#categoryForm').on('submit', function(e) {
                e.preventDefault();

                let formAction = $('#submitBtn').text() ===  'Update' ? 'update' : 'store';
                let categoryId = $('#categoryId').val(); 

                let url  = formAction === 'update' ? "{{ route('categories.update', ':id') }}".replace(':id', categoryId) : "{{ route('categories.store') }}";
                $.ajax({
                    url: url,
                    type: formAction ===  'update' ? 'POST' : 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response);
                        console.log($('#submitBtn').text());
                        if (response.status === 'success') {
                            toastr.success(response.message).css('width','500px');
                            $('#categoryForm')[0].reset();
                            $('.subcategory').remove();
                            $('#subcategories').append('<div class="subcategory"><label for="subCategoryName">Subcategory Name : </label><input type="text" name="sub_category_name[]" class="subCategoryName"><label for="subCatergoryPrice">Subcategory Price : </label><input type="number" name="sub_category_price[]" class="subCategoryPrice"><button type="button" id="addSubcategory">+</button></div>');
                            $('#addSubcategory').off('click').on('click', function(){
                                appendSubCategory();
                            });
                            fetchCategories();
                            
                            $('input[name="category_id"]').remove();
                            $('#submitBtn').text('Submit');
                        }
                    }
                });
            });
            
            
            $(document).on('click', '.removeSubcategory', function(){
                let subCategoryId = $(this).data('id');

                if(subCategoryId){
                    let deleteInput = `<input type="hidden" name="delete_subcategory_ids[]" value="${subCategoryId}">`;
                    $('#categoryForm').append(deleteInput);
                }
                $(this).closest('.subcategory').remove();
                if($('.subcategory').length <= 5){
                    $('#addSubcategory').show();
                }
            });

            function searchCategories(query = '', minPrice = '', maxPrice = '') {
                $.ajax({
                    url: "{{ route('categories.search') }}",
                    type: "GET",
                    data: { query: query , min_price: minPrice, max_price: maxPrice},
                    success: function(response) {
                        if (response.status === 'success') {
                            let categories = response.categories;
                            let tableRows = '';

                            categories.forEach(function(category) {
                                let subcategories = '';

                                category.subcategories.forEach(function(subcategory) {
                                    subcategories += `<p>Name: ${subcategory.name}, Price: ${subcategory.price}</p>`;
                                });

                                tableRows += `
                                    <tr>
                                        <td>${category.name}</td>
                                        <td>${subcategories}</td>
                                        <td> 
                                            <button class="editBtn" data-id="${category.id}">Edit</button>
                                            <button class="deleteBtn" data-id="${category.id}">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            });

                            $('#categoryTable tbody').html(tableRows);
                        }
                    }
                });
            }

            $('#download').click(function() {
               download();
            });

            function download() {
                $.ajax({
                    url: "{{ route('categories.download') }}",
                    type: "GET",
                    success: function(response) {
                        if (response.status === 'success') {
                            let categories = response.categories;
                            let tableRows = '';

                            categories.forEach(function(category) {
                                let subcategories = '';

                                category.subcategories.forEach(function(subcategory) {
                                    subcategories += `<p>Name: ${subcategory.name}, Price: ${subcategory.price}</p>`;
                                });

                                tableRows += `
                                    <tr>
                                        <td>${category.name}</td>
                                        <td>${subcategories}</td>
                                    </tr>
                                `;
                            });

                            $('#categoryTable tbody').html(tableRows);
                            downloadTableAsPDF();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error loading categories');
                    }
                });
            }

            function downloadTableAsPDF() {
                html2canvas(document.querySelector("#categoryTable"), {
                    onrendered: function(canvas) {
                        var imgData = canvas.toDataURL('image/png');
                        var pdf = new jsPDF('p', 'mm', 'a4');
                        var imgWidth = 210; 
                        var pageHeight = 295;  
                        var imgHeight = canvas.height * imgWidth / canvas.width;
                        var heightLeft = imgHeight;

                        var position = 0;

                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;

                        while (heightLeft >= 0) {
                            position = heightLeft - imgHeight;
                            pdf.addPage();
                            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                        }
                        pdf.save('categories.pdf');
                    }
                });
            }
            $(document).on('click', '.deleteBtn', function() {
                let categoryId = $(this).data('id');

                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this category!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                       
                        $.ajax({
                            url: "{{route('categories.delete', ':id')}}".replace(':id', categoryId),
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your category has been deleted.',
                                        'success'
                                    );
                                    fetchCategories();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong. Please try again later.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

        });
    </script>

</body>
</html>
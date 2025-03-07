<footer>

<script src="{{asset('sweetalert2/sweetalert2.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            var subCategoryCounter = 1;

            $('#addBtn').on('click' ,function(e){
                subCategoryCounter++ ;
                
                let newSubcategory = `
                <div class="sub_row">
                    <label for="sname">Name : </label>
                    <input type="text" name="sub_name[]" class="sname" placeholder="Enter sub category name" /><br/>
                    <small class="suberror"></small><br/><br/>

                    <label for="sprice">Price :  </label>
                    <input type="number" name="price[]" class="sprice" placeholder="Enter sub category price" /><button type="button" id="removeBtn">&times;</button><br/>
                    <small class="suberror"></small><br/><br/>
                </div>
                `;
                $('#sub_category').append(newSubcategory);
                
                if($('.sub_row').length >= 5){
                    $('#addBtn').hide();
                }
            });

            $(document).on('click', '#removeBtn' ,function(e){
                $(this).closest('.sub_row').remove();
                if($('.sub_row').length < 5){
                    $('#addBtn').show();
                }
            });

            $('#categoryForm').on('submit', function(e){
                e.preventDefault();
               
                var isValid = true;
                var cname = $('#cname').val().trim();
                if(cname == ''){
                    $('#cnameErr').show().html('Category name is required');
                    $('#cnameErr').css('color', 'red');
                    $('#cname').focus();
                    isValid = false;
                }else{
                    $('#cnameErr').hide();
                }
                
                $('.sname').each(function(){
                    console.log($(this).val());
                    if($(this).val().trim() == ''){
                        $(this).next('.suberror').text('Subcategory name is required').show().css('color', 'red');
                        isValid = false;
                    }else{
                        $(this).next('.suberror').hide();
                    }
                });

                $('.sprice').each(function(){
                    if($(this).val().trim() == ''){
                        $(this).next('.suberror').show().html('Subcategory price is required').css('color', 'red');
                        isValid = false;
                    }else if($(this).val().trim() <= 0){
                        $(this).next('.suberror').show().html('Subcategory price must be greated than 0').css('color', 'red');
                        isValid = false;
                    }else{
                        $(this).next('.suberror').hide();
                    }
                });


                

                if(isValid){
                    let formData = $('#categoryForm')[0];
                    $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ route('categories.store') }}",
                        data: formData,
                        processData:false,
                        contentType:false,
                        success: function(response) {
                            if(response.status === 'success'){
                                $('#form-success').text(response.message);
                                $('#form-success').css('color', 'green');
                                $('#categoryForm')[0].reset();
                                $('#sub_row').remove();
                                fetchCategories();
                            }
                        },
                    });
                }
            });

            fetchCategories();

            

            function fetchCategories()
            {
                $.ajax({
                    url: "{{ route('categories.fetch') }}",
                    type: "GET",
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
                        }
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
                    // Proceed with deletion
                    $.ajax({
                        url: `/categories/delete/${categoryId}`,
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
                                fetchCategories(); // Reload the table after deletion
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

            $(document).on('click','.editBtn',function(e){
                let categoryId = $(this).data('id');

                window.location.href = `/categories/edit/${categoryId}`;
            });
        });
    </script>
</footer>
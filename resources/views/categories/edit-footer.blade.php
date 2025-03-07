<footer>

<script>
        $(document).ready(function() {
            var subCategoryCounter = 0;
            // AJAX submit form for updating category and subcategories
            $('#submitEditForm').on('click', function(e) {
                e.preventDefault();
                let categoryId = $('#category_id').val();
                let formData = $('#editCategoryForm').serialize(); // Serialize the form data
                var isValid = true;
                var cname = $('#categoryName').val();
              
                $('.subcategory-row').each(function() {
                    var subcategoryId = $(this).data('id'); // Get the subcategory ID
                    var nameField = $('#sub_name_' + subcategoryId);  // Select subcategory name input
                    var nameErrorField = $('#snameErr_' + subcategoryId); // Select name error display element
                
                    var priceField = $('#sub_price_' + subcategoryId); // Select subcategory price input
                    var priceErrorField = $('#spriceErr_' +  subcategoryId); // Select price error display element

                    // Clear previous error messages
                    nameErrorField.text('');
                    priceErrorField.text('');
                    // Validate subcategory name (cannot be empty)
                    if (nameField.val().trim() === '') {
                        isValid = false;
                        nameErrorField.text('Subcategory name is required').css('color', 'red');
                        nameField.css('border', '1px solid red');
                        
                    } else {
                        nameField.css('border', '');
                    }

                    if(priceField.val().trim() === ''){
                        isValid = false;
                        priceErrorField.text('Subcategory price is required').css('color', 'red');
                        priceField.css('border', '1px solid red');
                        
                    }else{
                        if(priceField < 0 || priceField == 0){
                            isValid = false;
                            priceErrorField.text('Subcategory price must be greater than 0').css('color','red');
                            priceField.css('border', '1px solid red');
                            
                        }else{
                            priceField.css('border', '');
                        }
                    }
                });

                if(cname == ''){
                    $('#cnameErr').show().html('Category name is required');
                    $('#categoryName').focus();
                    $('#cnameErr').css('color', 'red');
                    $('#categoryName').css('border', '1px solid red');
                    isValid = false;
                }else{
                    $('#cnameErr').hide();
                }

                console.log(isValid);
                if(isValid){
                        $.ajax({
                        url: `/categories/update/${categoryId}`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                window.location.href = `/categories`; // Refresh the page after update
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
                    
            });

            $('.sub_category').on('click', '#addBtn' ,function(e){
                subCategoryCounter++ ;
                
                let newSubcategory = `
                <div class="sub_row" data-id="${subCategoryCounter}">
                    <label for="sname_${subCategoryCounter}">Name : </label>
                    <input type="text" name="sub_name[]" id="sname_${subCategoryCounter}" placeholder="Enter sub category name" /><br/>
                    <small id="snameErr_${subCategoryCounter}"></small><br/><br/>

                    <label for="sprice_${subCategoryCounter}">Price :  </label>
                    <input type="number" name="price[]" id="sprice_${subCategoryCounter}" placeholder="Enter sub category price" /> <button type="button" id="removeBtn">&times;</button> <br/>
                    <small id="spriceErr_${subCategoryCounter}"></small><br/><br/>
                </div>
                `;
                $('.sub_category').append(newSubcategory);
                
                if(subCategoryCounter >= 5){
                    $('#addBtn').hide();
                    console.log($('.sub_row').length);
                }
            });

           $('.sub_category').on('click', '#removeBtn' ,function(e){
                $(this).closest('.sub_row').remove();
                console.log($('.sub_row').length);
                subCategoryCounter--;
                if($('.sub_row').length < 5){
                    $('#addBtn').show();
                }
            });
        });
</script>

</footer>
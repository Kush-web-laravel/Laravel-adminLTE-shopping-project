<!DOCTYPE html>
<html lang="en">
@include('categories/edit-header')
<body>

    <form id="editCategoryForm">
        @csrf
        <label for="categoryName">Category Name:</label>
        <input type="text" id="categoryName" name="category_name" value="{{ $category->name }}"><br><br>
        <small class="error" id="categoryNameError" style="color : red; display:none;">Category name is required</small><br/><br/>
        <input type="hidden" id="category_id" value="{{ $category->id }}"><br><br>
        <div id="subcategories">
            <!-- Loop through existing subcategories -->
            @foreach($category->subcategories as $index => $subcategory)
            <div class="subcategory" data-id="{{ $subcategory->id }}">
                <input type="hidden" name="existing_subcategory_ids[]" value="{{ $subcategory->id }}">
                <label for="subCategoryName">Subcategory Name:</label>
                <input type="text" name="existing_sub_category_name[]" class="subCategoryName" value="{{ $subcategory->name }}">
                <small class="suberror" style="color : red; display:none;">Sub Category name is required</small><br/><br/>
                <label for="subCategoryPrice">Subcategory Price:</label>
                <input type="number" name="existing_sub_category_price[]" class="subCategoryPrice" value="{{ $subcategory->price }}">
                <small class="suberror" style="color : red; display:none;">Sub Category price must be greater than 0</small><br/><br/>
                @if($index == 0)
                <button type="button" id="addSubcategory">+</button><br><br>
                @endif
                @if($index > 0)
                <button type="button" class="removeSubcategory">X</button>
                @endif
                <input type="hidden" name="delete_subcategory_ids" id="delete_subcategory_ids">
            </div>
            @endforeach
        </div>

        <button type="submit">Update</button>
    </form>

    <script>
        $(document).ready(function(){
            let subcategoryIndex = {{ count($category->subcategories) }};
            console.log(subcategoryIndex);
            let deleteSubcategoryIds = [];
            function appendSubCategory()
            {
                $('#subcategories').append(`
                    <div class="subcategory">
                        <label for="subCategoryName">Subcategory Name:</label>
                        <input type="text" name="new_sub_category_name[]" class="subCategoryName">
                        <small class="suberror" style="color : red; display:none;">Sub Category name is required</small><br/><br/>
                        
                        <label for="subCategoryPrice">Subcategory Price:</label>
                        <input type="number" name="new_sub_category_price[]" class="subCategoryPrice">
                        <small class="suberror" style="color : red; display:none;">Sub Category price must be greater than 0</small>
                        <button type="button" class="removeSubcategory">X</button>

                    </div>
                `);
            }
            // Add a new subcategory row
            $('#addSubcategory').click(function(){
                subcategoryIndex++;
                appendSubCategory();
            });

            // Remove a subcategory row
            $(document).on('click', '.removeSubcategory', function(){
                let subcategoryId = $(this).data('id');
                if (subcategoryId) {
                    // Add the subcategory ID to the deletion array
                    deleteSubcategoryIds.push(subcategoryId);
                    $('#delete_subcategory_ids').val(deleteSubcategoryIds.join(','));
                }
                $(this).parent().remove();
            });

            // jQuery validation on form submission
            $('#editCategoryForm').on('submit', function(e){
                let isValid = true;
                var categoryId = $(this).data('id');
                // Validate the category name
                if ($('#categoryName').val().trim() === '') {
                    $('#categoryNameError').show();
                    isValid = false;
                }else{
                    $('#categoryNameError').hide();
                }

                // Validate all dynamically added subcategory fields
                $('.subCategoryName').each(function(){
                    if ($(this).val().trim() === '') {
                        console.log($(this).val().trim());
                        $(this).next('.suberror').show();
                        console.log($(this).next('.error'));
                        isValid = false;
                        
                    }else{
                        $(this).next('.suberror').hide();
                    }
                });

                $('.subCategoryPrice').each(function(){
                    if ($(this).val() === '' || $(this).val() <= 0) {
                        $(this).next('.suberror').show();
                        isValid = false;
                        
                    }else{
                        $(this).next('.error').hide();
                    }
                });

                if (!isValid) {
                   e.preventDefault();
                }else{
                   
                    console.log($(this));
                    // $.ajax({
                    //     headers:{
                    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //     },
                    //     url: "{{ route('categories.update', ':id' ) }}".replace(':id', $('#category_id').val()), 
                    //     type: "POST",
                    //     data: $(this).serialize(),
                    //     success: function(response) {
                    //         alert('Category updated successfully!');
                          
                    //     },
                    //     error: function(xhr) {
                    //         alert('An error occurred while updating the category.');
                    //     }
                    // });
                }
            });
        });
    </script>

</body>
</html>
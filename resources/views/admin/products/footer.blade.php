<div class="modal" id="updateProfileModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Update Profile Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="name">Name : </label>
        <input type="text" id="name" name="name" value="{{auth()->user()->name}}" placeholder="Enter your name" class="form-control">
        <small id="nameError" class="text-danger"></small>
        <label for="email">Email : </label>
        <input type="email" id="email" name="email" value="{{auth()->user()->email}}" placeholder="Enter your email" class="form-control">
        <small id="emailError" class="text-danger"></small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="checkEmail">Update Profile</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="changePasswordModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Change Password</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="{{auth()->user()->id}}"/>
        <div>
            <label for="old_password">Old Password</label>
            <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Enter your old password">
        </div>
        <div>
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your new password">
        </div>
        <div>
            <label for="c_pwd">Confirm Password</label>
            <input type="password" name="password_confirmation" id="c_pwd" style="margin-bottom: 7px" class="form-control" placeholder="Enter your confirmation password"><br/>
            <span id="passwordErr" style="color: red; font-size: 13px;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="changePassword">Update Password</button>
      </div>
    </div>
  </div>
</div>
<footer class="app-footer"> <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Anything you want</div> <!--end::To the end--> <!--begin::Copyright--> <strong>
                Copyright &copy; 2014-2024&nbsp;
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
</footer> <!--end::Footer-->
<script>

        $(document).ready(function(){

            $('#changePassword').click(function(event){
                event.preventDefault();

                if($('#password').val() == '' ||  $('#c_pwd').val() == '' ||  $('#old_password').val() == ''){
                    $('#passwordErr').show().html('Passwords are required');
                }else{
                    if($('#password').val() != $('#c_pwd').val()){
                        $('#passwordErr').show().html('Passwords do not match');
                    }
                }

                $.ajax({
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    url: '{{ route("change-password") }}',
                    type: 'POST',
                    data: {
                        _token: $('input[name=_token]').val(),
                        old_password: $('input[name=old_password]').val(),
                        password: $('input[name=password]').val(),
                        password_confirmation: $('input[name=password_confirmation]').val(),
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect_url;
                        }else{
                            $('#passwordErr').text(response.message);
                        }
                    },
                });
            });

            $('#checkEmail').click(function(e){
                e.preventDefault();

                if($('#name').val()=='' || $('#email').val()==''){
                    $('nameError').show().html('Name is required');
                    $('emailError').show().html('Email is required');
                }else{
                    const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;

                    if(!regex.test($('#email').val())){
                        $('#emailError').show().html('Invalid email address.Enter valid email address');
                    }else{
                        $('#emailError').hide();
                        $.ajax({
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{route('updateProfile')}}" ,
                            type: 'POST',
                            data:{
                                name: $('input[name=name]').val(),
                                email: $('input[name=email]').val(),
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    window.location.href = response.redirect_url;
                                }else{
                                    $('#emailError').text(response.message);
                                }
                            }
                        });
                    }
                }
            });

            $('#submitBtn').click(function(e){

                var temp=0;
                var productName = $('#productName').val();
                var productPrice = $('#productPrice').val();
                var productDescription = $('#productDetails').val();
                var productImage = $('#productImage')[0].files[0];

                if(productName == ''){
                    $('#nameErr').show().html('Product name is required');
                    temp++;
                }else{
                    $('#nameErr').hide();
                }

                if(productPrice == ''){
                    $('#priceErr').show().html('Product price is required');
                    temp++;
                }else{
                    if(productPrice < 0){
                        $('#priceErr').show().html('Product price should be greater than 0');
                        temp++;
                    }else{
                        $('#priceErr').hide();
                    }
                    
                }

                if(productDescription == ''){
                    $('#detailsErr').show().html('Product description is required');
                    temp++;
                }else{
                    $('#detailsErr').hide();
                }

                if (!productImage) {
                    $("#imageErr").show().html("Product image is required");
                    temp++;
                } else {
                    var validFileTypes = ["jpeg", "jpg", "png", "gif"];
                    var fileExtension = productImage.name.split('.').pop().toLowerCase();
                    if (!validFileTypes.includes(fileExtension)) {
                        $("#imageErr").show().html("Invalid File. Please upload a image file with .jpg, .jpeg, .png, .gif extension.");
                        temp++;
                    } else {
                        $("#imageErr").hide();
                    }
                }

                if(temp == 0){
                    var form = $('#addProducts')[0];
                    var data = new FormData(form);
                    $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{route('products.store')}}",
                        type: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(response){
                            if (response.status === 'success') {
                                    window.location.href = response.redirect_url;
                                    toastr.success(response.message);
                                    return true
                            }else{
                                    $('#formErr').text(response.message);
                            }
                        },
                    });
                }else{
                    e.preventDefault();
                    return false;
                }
            });

            var buyUrl = "{{ route('product.buy', '') }}";
            var cartUrl = "{{ route('cart.add', '') }}";
            var table = $('#productsTable').DataTable({
                "ajax": {
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    "url": "{{ route('products.show') }}",
                    "type": "GET",
                    "dataType": 'json',
                },
                "columns": [
                    { "data": "id" },
                    { "data": "productName" },
                    { "data": "productPrice" },
                    { "data": "productDetails" },
                    {
                        "data": "productImage", // Product Image
                        "render": function(data) {
                            return '<img src="{{ asset("storage/") }}/' + data + '" height="50"/>'; 
                        }
                    },
                    {
                        "data": null,
                        "render": function(data) {
                            var inCart = data.in_cart; 
                            var cartButton = inCart ? '' : `<a href="${cartUrl}/${data.id}" class="btn btn-sm btn-light cart-btn" id="cart-btn-${data.id}" data-id="${data.id}">Add to cart</a>`;

                            return `
                                <a href="/edit-products/${data.id}" class="btn btn-sm btn-success">Edit</a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${data.id}">Delete</button>
                                ${cartButton}
                                <a href="${buyUrl}/${data.id}" class="btn btn-sm btn-warning pay-btn" data-id="${data.id}">Buy now</a>
                            `;
                        }
                    }
                ]
            });


            $('#updateBtn').click(function(e){

                var temp=0;
                var productId = $('#productId').val();
                var productName = $('#productName').val();
                var productPrice = $('#productPrice').val();
                var productDescription = $('#productDetails').val();
                var productImage = $('#productImage')[0].files[0];


                $("#productImage").on('change', function(e){
                var existingFile = $('#existingFile').val();


                var image = $("#productImage").val();

                var extension = image.substring(image.lastIndexOf(".") + 1, image.length).toLowerCase();
                var validFilesTypes = ["jpeg", "jpg", "png", "gif"];
                var isValidFile = validFilesTypes.includes(extension);

                if(!image && existingFile){
                    $('#imageErr').hide();
                }else{
                    if (!isValidFile) {
                    $("#imageErr").show();
                    $("#imageErr").html("Invalid File. Please upload file with extension: " + validFilesTypes.join(", ") + ".");
                    }else{
                    $("#imageErr").hide();
                    }
                }
                });
                if(productName == ''){
                    $('#nameErr').show().html('Product name is required');
                    temp++;
                }else{
                    $('#nameErr').hide();
                }

                if(productPrice == ''){
                    $('#priceErr').show().html('Product price is required');
                    temp++;
                }else{
                    $('#priceErr').hide();
                }

                if(productDescription == ''){
                    $('#detailsErr').show().html('Product description is required');
                    temp++;
                }else{
                    $('#detailsErr').hide();
                }

                if(temp == 0){
                    var form = $('#updateProducts')[0];
                    var data = new FormData(form);
                    $.ajax({
                        hreaders:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{route('products.update')}}",
                        type: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(response){
                            if (response.status === 'success') {
                                    window.location.href = response.redirect_url;
                                    return true
                            }else{
                                    $('#formErr').text(response.message);
                            }
                        },
                    });
                }else{
                    e.preventDefault();
                    return false;
                }
            });

            $('#productsTable tbody').on('click', '.delete-btn', function() {
                var id = $(this).data('id'); 
                var row = $(this).closest('tr'); 

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/delete-products/' + id, 
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Refresh the DataTable
                                    $('#productsTable').DataTable().ajax.reload(null, false); 
                                    Swal.fire(
                                        'Deleted!',
                                        'Your product has been deleted.',
                                        'success'
                                    );
                                }
                            },
                            error: function(err) {
                                console.log(err.responseText);
                            }
                        });
                    }
                });
            });

            
        });
</script>
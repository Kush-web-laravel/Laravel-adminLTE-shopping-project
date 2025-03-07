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

            toastr.options = {
                "closeButton": true,
                "newestOnTop": true,
                "positionClass": "toast-top-right"
            };


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

            let grossTotal = 0, lessTotal = 0, netTotal = 0, touchTotal = 0, finalTotal = 0;
        
                $('#invoice-table').on('input', '.gross_weight', function(){
                    grossTotal = 0;
                    $('.gross_weight').each(function(){
                        const grossWeightValue = $(this).val();
                        const grossWeight = grossWeightValue !== '' ?  parseFloat(grossWeightValue) : 0;

                        if(grossWeight > 0){
                            grossTotal += grossWeight;
                        }
                    });
                    $('#grossTotal').text(grossTotal.toFixed(2));
                });

                $('#invoice-table').on('input', '.less_weight', function(){
                    lessTotal = 0;
                    $('.less_weight').each(function(){
                        const lessWeightValue = $(this).val();
                        const lessWeight = lessWeightValue !== '' ?  parseFloat(lessWeightValue) : 0;

                        if(lessWeight > 0){
                            lessTotal += lessWeight;
                        }
                    });
                    $('#lessTotal').text(lessTotal.toFixed(2));
                });
           
            $('#invoice-table').on('input', '.gross_weight, .less_weight', function() {
                const row = $(this).closest('.data-row');
                const grossWeightValue = row.find('.gross_weight').val();
                const lessWeightValue = row.find('.less_weight').val();

                const grossWeight = grossWeightValue !== "" ? parseFloat(grossWeightValue) : null;
                const lessWeight = lessWeightValue !== "" ? parseFloat(lessWeightValue) : null;


                // If both values are valid, calculate net weight
                if (grossWeight !== null && lessWeight !== null && lessWeight < grossWeight) {
                    const netWeight = grossWeight - lessWeight;
                    row.find('.net_weight').val(netWeight.toFixed(2));

                    let netTotal = 0;
                    $('.net_weight').each(function(){
                        const value = parseFloat($(this).val());
                        if(!isNaN(value)){
                            netTotal += value;
                        }
                    });
                    $('#netTotal').text(netTotal.toFixed(2));
                    calculateFinalWeight(row, netWeight); // Calculate final weight when net weight changes
                } else {
                    // Clear net weight if inputs are invalid
                    row.find('.net_weight').val('');
                    row.find('.final_weight').val(''); // Clear final weight if net weight is invalid

                    let netTotal = 0;
                    $('.net_weight').each(function(){
                        const value = parseFloat($(this).val());
                        if(!isNaN(value)){
                            netTotal += value;
                        }
                    });
                    $('#netTotal').text(netTotal.toFixed(2));
                }
            });

            $('#invoice-table').on('input', '.touch', function() {
                const row = $(this).closest('.data-row');
                let touchValue = parseFloat($(this).val());

                // Limit touch to 100 if it's greater
                if (touchValue > 100) {
                    touchValue = 100;
                    $(this).val(100);
                }
                touchTotal = 0;
                    $('.touch').each(function(){
                        const touchValue = $(this).val();
                        const touch = touchValue !== '' ?  parseFloat(touchValue) : 0;

                        if(touch > 0){
                            touchTotal += touch;
                        }
                    });
                $('#touchTotal').text(touchTotal.toFixed(2));

                const netWeightValue = row.find('.net_weight').val();
                const netWeight = netWeightValue !== "" ? parseFloat(netWeightValue) : null;

                calculateFinalWeight(row, netWeight, touchValue); // Calculate final weight based on current touch and net weight
            });

            function calculateFinalWeight(row, netWeight, touchValue=null)
            {
                if (netWeight !== null) {
                    if (touchValue === null) {
                        touchValue = parseFloat(row.find('.touch').val());
                    }

                    if (touchValue !== null && touchValue > 0) {
                        const finalWeight = (netWeight * touchValue) / 100;
                        row.find('.final_weight').val(finalWeight.toFixed(2));

                        let finalTotal = 0;
                        $('.final_weight').each(function(){
                            const value = parseFloat($(this).val());
                            if(!isNaN(value)){
                                finalTotal += value;
                            }
                        });
                        $('#finalTotal').text(finalTotal.toFixed(2));
                    } else {
                        row.find('.final_weight').val(''); // Clear if touch is invalid
                    }
                } else {
                    row.find('.final_weight').val(''); // Clear if net weight is invalid
                }
            }

            $('#invoiceForm').on('submit', function(e){
                e.preventDefault();

                // Reset totals for each submission
                grossTotal = 0;
                lessTotal = 0;
                netTotal = 0;
                touchTotal = 0;
                finalTotal = 0;

                var isValid = true;

                $('.data-row').each(function() {
                    const item = $(this).find('.items').val().trim();
                    const grossWeightValue = $(this).find('.gross_weight').val();
                    const lessWeightValue = $(this).find('.less_weight').val();
                    const touchValue = $(this).find('.touch').val();

                    const grossWeight = grossWeightValue !== "" ? parseFloat(grossWeightValue) : null;
                    const lessWeight = lessWeightValue !== "" ? parseFloat(lessWeightValue) : null;
                    const touch = touchValue !== "" ? parseFloat(touchValue) : null;

                    if(item === '') {
                        $(this).find('.itemsErr').text('Item name is required').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.itemsErr').hide();
                    }

                    if(grossWeightValue === "" || grossWeight === null) {
                        $(this).find('.grossErr').text('Gross weight is required').show().css('color', 'red');
                        isValid = false;
                    } else if(grossWeight <= 0) {
                        $(this).find('.grossErr').text('Gross weight should be greater than 0').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.grossErr').hide();
                    }

                    if(lessWeightValue === "" || lessWeight === null) {
                        $(this).find('.lessErr').text('Less weight is required').show().css('color', 'red');
                        isValid = false;
                    } else if(lessWeight < 0 || lessWeight >= grossWeight) {
                        $(this).find('.lessErr').text('Less weight should be less than gross weight').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.lessErr').hide();
                    }

                    if(touchValue === "" || touch === null) {
                        $(this).find('.touchErr').text('Touch weight is required').show().css('color', 'red');
                        isValid = false;
                    } else if(touch < 0) {
                        $(this).find('.touchErr').text('Touch weight should be greater than 0').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.touchErr').hide();
                    }

                    if(isValid) {
                        const netWeight = grossWeight - lessWeight;
                        const finalWeight = (netWeight * touch) / 100;

                        $(this).find('.net_weight').val(netWeight.toFixed(2));
                        $(this).find('.final_weight').val(finalWeight.toFixed(2));
                    }
                });

                const description = $('#description').val().trim();
                if(description === "") {
                    $('#descriptionErr').show().html('Description is required').css('color', 'red');
                    isValid = false;
                } else {
                    $('#descriptionErr').hide();
                }

                if(isValid) {

                   var formData = new FormData($('#invoiceForm')[0]);

                   $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url : "{{ route('invoice.store') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response){
                            if(response.status == 'success'){
                                window.location = response.redirect_url;
                                toastr.success(response.message).css('width','500px');
                            }else{
                                alert('There is some issue');
                            }
                        }
                   });
                }
            });

            $('#updateInvoiceForm').on('submit', function(e){
                e.preventDefault();

                let formData =  new FormData($('#updateInvoiceForm')[0]);
                let invoiceId = $('#invoice_id').val();
                let urlEdit = "{{ route('invoice.update', ':id') }}".replace(':id', invoiceId);

                grossTotal = 0;
                lessTotal = 0;
                netTotal = 0;
                touchTotal = 0;
                finalTotal = 0;

                var isValid = true;

                $('.data-row').each(function() {
                    const item = $(this).find('.items').val().trim();
                    const grossWeightValue = $(this).find('.gross_weight').val();
                    const lessWeightValue = $(this).find('.less_weight').val();
                    const touchValue = $(this).find('.touch').val();

                    const grossWeight = grossWeightValue !== "" ? parseFloat(grossWeightValue) : null;
                    const lessWeight = lessWeightValue !== "" ? parseFloat(lessWeightValue) : null;
                    const touch = touchValue !== "" ? parseFloat(touchValue) : null;

                    if(item === '') {
                        $(this).find('.itemsErr').text('Item name is required').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.itemsErr').hide();
                    }

                    if(grossWeightValue === "" || grossWeight === null) {
                        $(this).find('.grossErr').text('Gross weight is required').show().css('color', 'red');
                        isValid = false;
                    } else if(grossWeight <= 0) {
                        $(this).find('.grossErr').text('Gross weight should be greater than 0').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.grossErr').hide();
                    }

                    if(lessWeightValue === "" || lessWeight === null) {
                        $(this).find('.lessErr').text('Less weight is required').show().css('color', 'red');
                        isValid = false;
                    } else if(lessWeight < 0 || lessWeight >= grossWeight) {
                        $(this).find('.lessErr').text('Less weight should be less than gross weight').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.lessErr').hide();
                    }

                    if(touchValue === "" || touch === null) {
                        $(this).find('.touchErr').text('Touch weight is required').show().css('color', 'red');
                        isValid = false;
                    } else if(touch < 0) {
                        $(this).find('.touchErr').text('Touch weight should be greater than 0').show().css('color', 'red');
                        isValid = false;
                    } else {
                        $(this).find('.touchErr').hide();
                    }

                    if(isValid) {
                        const netWeight = grossWeight - lessWeight;
                        const finalWeight = (netWeight * touch) / 100;

                        $(this).find('.net_weight').val(netWeight.toFixed(2));
                        $(this).find('.final_weight').val(finalWeight.toFixed(2));

                        grossTotal += grossWeight;
                        lessTotal += lessWeight;
                        netTotal += netWeight;
                        touchTotal += touch;
                        finalTotal += finalWeight;
                    }
                });
                const description = $('#description').val().trim();
                if(description === "") {
                    $('#descriptionErr').show().html('Description is required').css('color', 'red');
                    isValid = false;
                } else {
                    $('#descriptionErr').hide();
                }
                  
                if(isValid){
                    $('#grossTotal').text(grossTotal.toFixed(2));
                    $('#lessTotal').text(lessTotal.toFixed(2));
                    $('#netTotal').text(netTotal.toFixed(2));
                    $('#touchTotal').text(touchTotal.toFixed(2));
                    $('#finalTotal').text(finalTotal.toFixed(2));
                    $.ajax({
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url : urlEdit,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response){
                            if(response.status == 'success'){
                                window.location = response.redirect_url;
                                toastr.success(response.message).css('width', '500px');
                            }
                        },
                        error:function(xhr){
                            console.log(xhr.responseText)
                            alert(xhr.responseText);
                        }
                    });
                }
                
            });

            $(document).on('click', '.delete-btn', function() {
                let invoiceId = $(this).data('id');
                var row = $(this).closest('tr'); 
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
                            url: "{{route('invoice.delete', ':id')}}".replace(':id', invoiceId),
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    row.remove();
                                    toastr.success(response.message).css('width', '500px');
                                    fetchInvoice();
                                    Swal.fire(
                                        'Deleted!',
                                        'Your invoice details has been deleted.',
                                        'success'
                                    );
                                    
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
            // Add new row
            $('#invoice-table').on('click', '.add-btn', function(){
                const newRow = $('.data-row:first').clone();
                newRow.find('input').val('');
                newRow.find('.add-btn').removeClass('add-btn').addClass('remove-btn').val('−');
                const rowCount = $('#invoice-table .data-row').length + 1;
                newRow.find('.row-number').text(rowCount);
                newRow.insertBefore('#total');
            });

            // Remove row
            $('#invoice-table').on('click', '.remove-btn', function() {
                $(this).closest('.data-row').remove();
                $('#invoice-table .data-row').each(function(index) {
                    $(this).find('.row-number').text(index + 1);
                });
                updateTotals();
            });

            // Automatically set touch to 100 if greater than 100
            $('#invoice-table').on('input', '.touch', function() {
                let touchValue = parseFloat($(this).val());
                if(touchValue > 100) {
                    $(this).val(100);
                }
            });

            const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            function generateInvoiceNumber(length) {
                let result = ' ';
                const charactersLength = characters.length;
                for ( let i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                $('#invoiceForm').find('#invoiceNumber').val(result);
            }

            generateInvoiceNumber(5);
            fetchInvoice();
            function fetchInvoice()
            {
                
                $.ajax({
                    type: 'GET',
                    url: "{{ route('invoice.show') }}",
                    success: function(response){
                        $('#invoiceList tbody').empty();
                        var editUrl = "{{ route('invoice.edit', '') }}"; // Store base URL without ID
                        var downloadUrl = "{{ route('invoice.download', '') }}";
                        var exportUrl = "{{ route('invoice.export', '') }}";
                        $.each(response, function(index, invoice){
                            $('#invoiceList tbody').append(
                                `
                                    <tr>
                                        <td>${index+1}</td>
                                        <td>${invoice.invoice_number}</td>
                                        <td>
                                            <a href="${exportUrl}/${invoice.id}" class="btn btn-info"> Export Invoice (CSV) </a>
                                            <a href="${downloadUrl}/${invoice.id}" class="btn btn-dark"> Download Invoice </a>
                                            <a href="${editUrl}/${invoice.id}" class="edit-btn btn btn-success">Edit</a>
                                            <button class="delete-btn btn btn-danger" data-id="${invoice.id}">Delete</button>
                                        </td>
                                    </tr>
                                `
                            );
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error fetching details', error)
                        alert("Failed to fetch details");
                    }
                });
            }  

            function updateTotals() {
                let grossTotal = 0, lessTotal = 0, netTotal = 0, touchTotal = 0, finalTotal = 0;
                $('#updateInvoiceForm table tbody tr').each(function() {
                    grossTotal += parseFloat($(this).find('.gross_weight').val()) || 0;
                    lessTotal += parseFloat($(this).find('.less_weight').val()) || 0;
                    netTotal += parseFloat($(this).find('.net_weight').val()) || 0;
                    touchTotal += parseFloat($(this).find('.touch').val()) || 0;
                    finalTotal += parseFloat($(this).find('.final_weight').val()) || 0;
                });
                $('#grossTotal').text(grossTotal.toFixed(2));
                $('#lessTotal').text(lessTotal.toFixed(2));
                $('#netTotal').text(netTotal.toFixed(2));
                $('#touchTotal').text(touchTotal.toFixed(2));
                $('#finalTotal').text(finalTotal.toFixed(2));
            }
                updateTotals();
        });
</script>
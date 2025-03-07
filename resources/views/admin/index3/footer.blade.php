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
        });
    </script>
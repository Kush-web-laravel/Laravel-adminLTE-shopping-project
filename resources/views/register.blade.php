@extends('admin.register.app')

@section('content')

<div class="register-box">
        <div class="register-logo"> <a href="{{route('admin.index2')}}"><b>Admin</b>LTE</a> </div> <!-- /.register-logo -->
        <div class="card">
            <div class="card-body register-card-body">
                <p class="register-box-msg">Register a new membership</p>
                <form id="registerForm">
                @csrf
                    <div class="input-group mb-3"> <input type="text" id="name" class="form-control" name = "name" placeholder="Full Name">
                        <div class="input-group-text"> <span class="bi bi-person"></span></div>
                    </div>
                    <span id="nameErr" style="color: red"></span><br/>
                    @error('name')
                            <span style="color: red;">{{$message}}</span>
                    @enderror
                    <div class="input-group mb-3"> <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                        <div class="input-group-text"> <span class="bi bi-envelope"></span></div>
                    </div>
                    <span  id="emailErr" style="color: red"></span><br/>
                    @error('email')
                            <span style="color: red;">{{$message}}</span>
                    @enderror
                    <div class="input-group mb-3"> <input type="password"class="form-control"  name="password" id="password"  placeholder="Password">
                        <div class="input-group-text"> <span class="bi bi-lock-fill"></span></div> 
                    </div> <!--begin::Row-->
                    <span id="passwordErr" style="color: red"></span><br/>
                    @error('password')
                            <span style="color: red;">{{$message}}</span>
                    @enderror
                    <div class="row">
                        <div class="col-8">
                            <div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"> <label class="form-check-label" for="flexCheckDefault">
                                    I agree to the <a href="#">terms</a> </label> </div>
                        </div> <!-- /.col -->
                        <div class="col-4">
                            <div class="d-grid gap-2"> <button type="submit" class="btn btn-primary" id="signIn">Sign In</button></div>
                        </div> <!-- /.col -->
                    </div> <!--end::Row-->
                </form>
                <div class="social-auth-links text-center mb-3 d-grid gap-2">
                    <p>- OR -</p> <a href="#" class="btn btn-primary"> <i class="bi bi-facebook me-2"></i> Sign in using Facebook
                    </a> <a href="#" class="btn btn-danger"> <i class="bi bi-google me-2"></i> Sign in using Google+
                    </a>
                </div> <!-- /.social-auth-links -->
                <p class="mb-0"> <a href="{{route('admin.login')}}" class="text-center">
                        I already have a membership
                    </a> </p>
            </div> <!-- /.register-card-body -->
        </div>
    </div> <!-- /.register-box --> <!--begin::Third Party Plugin(OverlayScrollbars)-->
@endsection
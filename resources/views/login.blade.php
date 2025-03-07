@extends('admin.login.app')

@section('content')

<div class="login-box">
        <div class="login-logo"> <a href="{{route('admin.index2')}}"><b>Admin</b>LTE</a> </div> <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form id="loginForm">
                    @csrf
                    <div class="input-group mb-3"> <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                        <div class="input-group-text"> <span class="bi bi-envelope"></span> </div>
                    </div>
                    <span  id="emailErr" style="color: red"></span><br/>
                    @error('email')
                        <span class="error-message" style="color: red;">{{$message}}</span>
                    @enderror
                    <div class="input-group mb-3"> <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        <div class="input-group-text"> <span class="bi bi-lock-fill"></span> </div>
                    </div> <!--begin::Row-->
                    <span id="passwordErr" style="color: red"></span><br/>
                    <div class="row" style="margin-bottom : 15px">
                        <div class="col-8">
                            <div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"> <label class="form-check-label" for="flexCheckDefault">
                                    Remember Me
                                </label> </div>
                        </div> <!-- /.col -->
                        <div class="col-4">
                            <div class="d-grid gap-2"> <button type="submit" class="btn btn-primary" id="signIn">Sign In</button> </div>
                        </div> <!-- /.col -->
                    </div> <!--end::Row-->
                    <span  id="loginErr" style="color: red"></span><br/>
                </form>
                <div class="social-auth-links text-center mb-3 d-grid gap-2">
                    <p>- OR -</p> <a href="#" class="btn btn-primary"> <i class="bi bi-facebook me-2"></i> Sign in using Facebook
                    </a> <a href="#" class="btn btn-danger"> <i class="bi bi-google me-2"></i> Sign in using Google+
                    </a>
                </div> <!-- /.social-auth-links -->
                <p class="mb-1"> <a href="" data-bs-toggle="modal"
data-bs-target="#forgotPasswordModal">I forgot my password</a> </p>
                <p class="mb-0"> <a href="{{route('admin.register')}}" class="text-center">
                        Register a new membership
                    </a> </p>
            </div> <!-- /.login-card-body -->
        </div>
    </div> <!-- /.login-box --> <!--begin::Third Party Plugin(OverlayScrollbars)-->
  
    @endsection
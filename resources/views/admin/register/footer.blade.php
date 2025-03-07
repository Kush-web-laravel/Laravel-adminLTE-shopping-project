<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{asset('adminlte-v4.0.0-beta2-with-dist/dist/js/adminlte.js')}}"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script> <!--end::OverlayScrollbars Configure--> <!--end::Script-->
     <script>
        $(document).ready(function(){
            $('#registerForm').on('submit', function(e){

            e.preventDefault();

                var temp = 0;
                var name = $('#name').val();
                var email = $('#email').val();
                var password = $('#password').val();

                if(name == ''|| name.length < 1){
                    $('#nameErr').show().html('Name is required');
                    temp++;
                }else{
                    $('#nameErr').hide();
                }

                if(email == ''|| email.length < 1){
                    $('#emailErr').show().html('Email is required');
                    temp++;
                }else{
                    const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if(!regex.test(email)){
                        $('#emailErr').show().html('Invalid email address.Enter valid email address');
                        temp++;
                    }else{
                        $('#emailErr').hide();
                    }
                }

                if(password == '' || password.length == 0){
                    $('#passwordErr').show().html('Password is required');
                    temp++;
                }else{
                    $('#passwordErr').hide();
                }

                if(temp > 0){
                    return false;
                }else{
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{route('register')}}",
                        data: $(this).serialize(),
                        dataType : 'json',
                        success: function(data){
                            if(data.status){
                                window.location = data.redirect;
                                return true;
                            }
                        }
                    });
                }
            });
        });
    </script>
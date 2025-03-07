<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

@include('admin/products/header')

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        @include('admin/products/navbar')
        @include('admin/products/sidebar')
            @yield('content')
        @include('admin/products/footer')
    </div> <!--end::App Wrapper--> <!--begin::Script--> <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{asset('adminlte-v4.0.0-beta2-with-dist/dist/js/adminlte.js')}}"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script src="{{asset('Datatables/datatables.min.js')}}"></script>
    <script src="{{asset('sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('toastr/toastr.min.js')}}"></script>
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
    </script> <!--end::OverlayScrollbars Configure--> <!-- OPTIONAL SCRIPTS --> <!-- apexcharts -->
    <script>
        $(document).ready(function(){
            $('#buyProduct').on('submit', function(e){
                
                var quantity =  $('#productQuantity').val();
                    if(quantity == ''){
                        e.preventDefault();
                        $('#buyProduct #quantityErr').show().html('Quantity field is required').css('color', 'red');
                    }else{
                        if(quantity <= 0){
                            e.preventDefault();
                            $('#buyProduct #quantityErr').show().html('Quantity field should be greater then 0').css('color', 'red');
                        }else{
                            if(quantity > 1000){
                                e.preventDefault();
                                $('#buyProduct #quantityErr').show().html('Quantity field should be maximum 1000').css('color', 'red');
                            }else{
                                $('#buyProduct #quantityErr').hide();
                            }
                        }
                    }
            });
            $('#buyProduct #productQuantity').on('change', function(){
                    var quantity =  $(this).val();
                    var isValid = true;
                    if(quantity <= 0){
                            $('#buyProduct #quantityErr').show().html('Quantity field should be greater then 0').css('color', 'red');
                            isValid =false;
                        }else{
                            if(quantity > 1000){
                                $('#buyProduct #quantityErr').show().html('Quantity field should be maximum 1000').css('color', 'red');
                                isValid = false;
                            }else{
                                $('#buyProduct #quantityErr').hide();
                            }
                        }
                        if(isValid){
                            var amount = quantity * $('#productPrice').val();
                            $('#buyProduct #payBtn').val("Pay ₹ "+ amount);
                        }else{
                            $('#buyProduct #payBtn').val("Pay");
                        }
            });

            var originalTotalCost = parseFloat($('#totalCost').text());
            var discountApplied = false;

            $('#cartTable').on('change', '.quantity-input', function() {
                var hasQuantityError = false;

                $('#cartTable tbody tr').each(function() {
                    var quantity = parseInt($(this).find('.quantity-input').val());
                    $(this).find('input[name="product_quantity"]').val(quantity);
                    $(this).find('.quantity-input').val(quantity);
                    console.log(parseInt($(this).find('input[name="product_quantity"]').val()));
                    var $quantityErr = $(this).find('.quantityErr');
                    if (quantity <= 0) {
                        $quantityErr.show().html('Quantity should be at least 1').css('color', 'red');
                        hasQuantityError = true;
                    } else {
                        if(quantity > 1000){
                            $quantityErr.show().html('Quantity should not be more than 1000').css('color','red');
                            hasQuantityError = true;
                        }else{
                            $quantityErr.hide();
                        }
                    }
                });

                if (!hasQuantityError) {
                    updateTotalCost();
                    var $row = $(this).closest('tr');
                    var productId = $row.find('input[name="product_id"]').val();
                    var newQuantity = $(this).val();

                    $.ajax({
                        url: '/update-cart-quantity',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            quantity: newQuantity
                        },
                        success: function(response) {
                            if (response.success) {
                                $row.find('input[name="product_quantity"]').val(newQuantity);
                                console.log('Quantity updated successfully');
                            } else {
                                console.log('Failed to update quantity');
                            }
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr.responseText);
                        }
                    });
                }
            });

            $('#cartTable #discount').on('change', function() {
                var discount = $('#cartTable #discount').val();
                var $discountErr = $('#discountErr');

                if (discount == 'GET20') {
                    $discountErr.hide();
                    discountApplied = true;
                    updateTotalCost();
                } else {
                    if(discount == ''){
                        $discountErr.hide();
                        discountApplied = true;
                        updateTotalCost();
                    }else{
                        $discountErr.show().html('Enter correct offer code to get discount').css('color', 'red');
                        discountApplied = false;
                        updateTotalCost();
                    }
                }
            });

            function updateTotalCost() {
                var totalCost = 0;
                $('#cartTable tbody tr').each(function() {
                    
                    var price = parseFloat($(this).find('input[name=product_price').val());
                    var quantity = parseInt($(this).find('.quantity-input').val());
                    if (!isNaN(price) && !isNaN(quantity)) {
                        totalCost += price * quantity;
                    }
                });

                if (discountApplied) {
                  
                    var discount = $('#cartTable #discount').val();
                    if (discount == 'GET20') {
                        totalCost = totalCost - (totalCost * 20 / 100);
                    }
                }

                $('#totalCost').text(totalCost.toFixed(2));
            }


            var rows = $('#cartTable .cart-item').length
            console.log(rows);

            if(rows == 0){
                $('#cart-buy').attr('disabled', true);
                $('#discount').attr('disabled', true);
            }
            
            $('.remove-btn').click(function() {
                var id = $(this).attr('data-id');
                var token = '{{ csrf_token() }}';

                var removeUrl = "{{ route('cart.remove', ':id') }}".replace(':id', id);

                $.ajax({
                    url: removeUrl,
                    type: 'DELETE',
                    data: {
                        _token: token
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#cart-item-' + id).remove();
                            rows--;
                            console.log(rows);
                           
                            var totalCost = 0;
                            $('tbody tr').each(function() {
                                var price = parseFloat($(this).find('input[name=product_price]').val());
                                var quantity = parseInt($(this).find('input[name=product_quantity]').val());
                                if (!isNaN(price) && !isNaN(quantity)) {
                                    totalCost += price * quantity;
                                }
                            });
                            $('#totalCost').text(totalCost.toFixed(2));
                        } else {
                            alert('Failed to remove item from cart.');
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while removing the item.');
                    }
                });
            });

            
        });
    </script>
</body><!--end::Body-->

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminLTE | Dashboard v3 | Change Password</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <form>
        @csrf
        <h2>Change Password</h2>

        <input type="hidden" name="id" value="{{auth()->user()->id}}"/>
        <div>
            <label for="old_password">Old Password</label>
            <input type="password" name="old_password" id="old_password">
        </div>

        <div>
            <label for="password">New Password</label>
            <input type="password" name="password" id="password">
        </div>

        <div>
            <label for="c_pwd">Confirm Password</label>
            <input type="password" name="password_confirmation" id="c_pwd" style="margin-bottom: 7px"><br/>
            <span id="passwordErr" style="color: red; font-size: 13px;">
        </div>

        <button type="submit">Reset Password</button>
    </form>

    <script>
        $(document).ready(function(){
            $('form').submit(function(event){
                event.preventDefault();

                if($('#password').val() == '' ||  $('#c_pwd').val() == '' ||  $('#old_password').val() == ''){
                    $('#passwordErr').show().html('Passwords are required');
                }else{
                    if($('#password').val() != $('#c_pwd').val()){
                        $('#passwordErr').show().html('Passwords do not match');
                    }
                }

                $.ajax({
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
        });
    </script>
</body>
</html>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\SendResetPassword;
use Session;

class WebAuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);

        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            "status" =>  true,
            "redirect" => url('/login')
        ]);
    }

    public function login(Request $request)
    {
       
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json([
                "status" => true,
                "redirect" => url('/index3'),
            ]);
        }else{
            return response()->json([
                "status" => false,
                "errors" => ["Invalid credentials. Please check your email and password."]
            ], 401);
        }
    }

    public function dashboard(){
        if(Auth::check()){
            return view('index3');
        }
        
        return redirect("/login");
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return Redirect('/login');
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
    
        // Check if the email exists in the users table
        $user = User::where('email', $email)->first();

        if ($user) {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]);

            Mail::to($user->email)->send(new SendResetPassword($token, $user->email));
            
            return response()->json(['status' => 'success', 'message' => 'Password reset link has been sent to your email.']);
        } else {
            return response()->json(['status' => 'error', 'message' =>  'Email not found.']);
        }
    }

    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        return view('password.reset', ['token' => $token, 'email' => $email]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if ($reset) {
            // Update the password
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the password reset token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Instead of a redirect, return a JSON response
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('login')  // Provide a redirect URL
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token or email'
            ], 400);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'The old password is incorrect.']);
        }
        if (strcmp($request->get('old_password'), $request->password) == 0) {
            return response()->json(['status' => 'error', 'message' => 'New Password cannot be same as your current password.']);
        }

        User::whereId($user->id)->update([
            'password' => Hash::make($request->password)
        ]);
        return response()->json(['status' => 'success', 'redirect_url' => route('admin.index3')]);

    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id
        ]);

        $user = Auth::user();

        User::whereId($user->id)->update([
            'name' => $request->name,
            'email' =>  $request->email
        ]);
        return response()->json(['status' => 'success', 'redirect_url' => route('admin.index3')]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class ApiAuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if($user->save()){
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully!'
            ],200);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Provide proper details']);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $user = $request->user();
        $tokenResult =  $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'message' => 'Logged in successfully !'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json(['user' => $request->user()],200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!'
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'The current password is incorrect.']);
        }
        if (strcmp($request->get('current_password'), $request->password) == 0) {
            return response()->json(['status' => 'error', 'message' => 'New Password cannot be same as your current password.']);
        }

        User::whereId($user->id)->update([
            'password' => Hash::make($request->password)
        ]);
        return response()->json(['status' => 'success', 'message' => 'Password updated successfully']);

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
        return response()->json(['status' => 'success', 'message' => 'Profile updated successfully']);
    }
}

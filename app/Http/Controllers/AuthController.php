<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // register user
    public function register(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:6',
        'address' => 'required|string|max:255',
        'phone' => 'required|integer|min:6',
        'role' => 'required|string|in:user,admin'
    ]);

    if($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ], 400);
    }

    // Create new user
    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    
    $success['name'] = $user->name;

    return response()->json([
        'success' => true,
        'message' => 'Registration successful',
        'data' => $success
    ], 201);
}

    // Function to login a user
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors()
            ], 400);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $success['token'] = $auth->createToken('auth_token')->plainTextToken;
            $success['name'] = $auth->name;
            $success['email'] = $auth->email;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $success
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login failed, check your email and password',
                'data' => null
            ], 401);
        }
    }

    // function to logout (erase current user token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully Logged Out.']);
    }
}
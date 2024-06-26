<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->input(key:'name'),
            'email' => $request->input(key:'email'),
            'password' => Hash::make($request->input(key:'password')),
        ]);

        return $user;
    }

    public function login(Request $request)
    {
        if(!Auth::attempt([
            'email' => $request->input(key:'email'),
            'password' => $request->input(key:'password'),
        ])){
            $output['success'] = false;
            $output['message'] = "Invalid credentials. Please check & try again.";
            $output['data'] = null;
            return $output;
        }
        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = Cookie('jwt', $token, 60*24);
        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ])->withCookie($cookie);
    }


    public function user()
    {
        return Auth::user();
    }

    public function logout(Request $request)
    {
        $cookie = Cookie::forget('jwt');
        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
            'data' => null
        ])->withCookie($cookie);
    }
}

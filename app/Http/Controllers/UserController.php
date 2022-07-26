<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return  $request->user();
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = md5( time() ). '_' .md5($request->email);
            $user->forceFill([
                'api_token' => $token
            ])->save();
            return response()->json([
                'token' => $token
            ]);
        }
        return response()->json([
            'message' => 'Belum terdaftar'
        ], 401);

    }

       public function logout(Request $request)
        {
            $request->user()->forceFill([
                'api_token' => null
            ])->save();
            return response()->json([
                'message' => 'Logout Success'
            ], 200);
        }
}

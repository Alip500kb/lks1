<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authenticate extends Controller
{
    public function singup(Request $request) {
        $valid = Validator::make($request->all(), [
            'name' => 'required|max:20|min:3|unique:users,name',
            'email' => 'required|min:6|max:36|unique:users,email|email',
            'password' => 'required|min:6|max:36',
            'password_confirmation' => 'required|same:password'
        ]);

        if($valid->fails()) {
            return response()->json($valid->errors(), 422);
        }

        $create_user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'message' => 'registrasi berhasil',
            'data' => [
                'name' => $request->name,
                'email' => $request->email,
                'created_at' => $create_user->created_at
            ],
            'token' => $create_user->createToken('signup')->plainTextToken
        ]);
    }
    //get ready for my arrival worms
    public function singin(Request $request) {
        $valid = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif (!Auth::attempt($request->all())) {
            return response()->json([
                'status' => 'gagal',
                'message' => 'akun tidak ditemukan atau password salah'
            ],401);
        }

        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'login telah berhasil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'token' => $user->createToken('login')->plainTextToken
        ]);
    }

    public function singout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ],200);
    }

    public function getme(Request $request) {
        $pengguna = $request->user();
        return response()->json([
            'data' => [
            'id' => $pengguna->id,
            'name' => $pengguna->name,
            'email' => $pengguna->email,
            'created_at' => $pengguna->created_at
            ]
        ],200);
    }
}

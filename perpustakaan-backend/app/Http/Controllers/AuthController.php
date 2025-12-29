<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // =======================
    // REGISTER
    // =======================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Karena kolom username wajib, tapi frontend tidak mengirim username
        $base = Str::slug($request->name, '');
        if ($base === '') $base = 'user';

        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }

        $user = User::create([
            'full_name' => $request->name,     // <-- sesuai kolom DB
            'username'  => $username,          // <-- auto generate
            'email'     => $request->email,
            'role'      => 'member',
            'password'  => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Register berhasil',
            'user' => [
                'id'       => $user->id,
                'name'     => $user->full_name, // frontend tetap lihat sebagai "name"
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
            ]
        ], 201);
    }

    // =======================
    // LOGIN
    // =======================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // hapus token lama
        $user->tokens()->delete();

        // buat token baru
        $token = $user->createToken('frontend')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->full_name,  // <-- jangan $user->name
                'username'  => $user->username,
                'email'     => $user->email,
                'role'      => $user->role ?? 'member'
            ]
        ], 200);
    }

    // =======================
    // LOGOUT
    // =======================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
     public function register(Request $req) {
        $data = $req->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>bcrypt($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token]);
    }

    public function login(Request $req) {
        $req->validate(['email'=>'required|email','password'=>'required']);
        $user = User::where('email',$req->email)->first();
        if (! $user || ! Hash::check($req->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json(['user'=>$user,'token'=>$token]);
    }

    public function logout(Request $req) {
        $req->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'logged out']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $isValidPassword = Hash::check($request->password, $user->password);
            if ($isValidPassword) {

                $token = $this->generateToken($user);
                return response()->json([
                    'token' => $token
                ]);
            }
        }

        return response()->json(['message' => 'invalid credentials']);
    }

    private function generateToken($user)
    {
        $jwtKey = env('JWT_KEY');
        $jwtExpired = env('JWT_EXPIRED');

        $now = now()->timestamp;
        $exp = now()->addDays($jwtExpired)->timestamp;
        $payload = [
            'iss' => 'stream.id',
            'nbf' => $now,
            'iat' => $now,
            'exp' => $exp,
            'user' => $user
        ];

        $token = JWT::encode($payload, $jwtKey, 'HS256');
        return $token;
    }
}

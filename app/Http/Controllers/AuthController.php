<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'role' => 'nullable|string|in:admin,user,supervisor',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? 'user',
        ]);
        $token = $user->createToken('token-name')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token-name', ['*'], now()->addMinutes(60))->plainTextToken;

            // verifier le nombre de reponses valides pour l'utilisateur ayant repondu
            $user = User::find($user->id);
            $number_of_validated_answers = Answer::where('user_id', $user->id)->where('is_validated', true)->count();
            if ($number_of_validated_answers >= 10) {
//               // si l'utilisateur est un admin, on ne lui ajoute pas de reputation
                if ($user->role !== 'admin' && $user->role !== 'supervisor') {
                    $user->update([
                        'role' => 'supervisor'
                    ]);

                }
            }

            return response()->json([
                'token' => $token,
                'expireAt' => now()->addMinutes(60)->format('Y-m-d H:i:s'),
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Deconnecté avec succès',
            'status' => '200'
        ], 200);
    }

    public function refresh (Request $request) {
        // Obtenez l'utilisateur authentifié
        $user = Auth::user();

//        return $user;

        // Révoquez les anciens jetons
        $user->tokens()->delete();

        // Créez un nouveau jeton
        $token = $user->createToken('token-name', ['*'], now()->addMinutes(60))->plainTextToken;

        return response()->json([
            'token' => $token,
            'tokenExpiry' => now()->addMinutes(60)->format('Y-m-d H:i:s'),
        ]);
    }
}

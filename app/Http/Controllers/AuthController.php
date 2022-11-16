<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('auth_token')->accessToken;
            $user->token = $token;
            return response()->json(['user' => $user], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function register(Request $request)
    {
        // if the user is register by himself or not speficied
        if ($request->type == ""){
            $request->type = 'C';
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'nif' => 'required|digits:9',
            'phone' => 'required|digits:9',
        ]);
        
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ]);

        $user->save();

        
        if ($request->type == 'C') {
            $customer = new Customer([
                'user_id' => $user->id,
                'nif' => $request->nif,
                'phone' => $request->phone,
                'points' => 0,
            ]);
            $customer->save();
        }

        return response()->json(['message' => 'Successfully created user!'], 200);
    }


    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     if (Auth::attempt($credentials)) {
    //         $user = User::where('email', $request->email)->first();
    //         $token = $user->createToken('token')->plainTextToken;
    //         return response()->json(['user' => $user,'token' => $token], 200);
    //     } else {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }
    // }
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required',
    //         'phone' => 'required',
    //     ]);
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     $customer = Customer::create([
    //         'user_id' => $user->id,
    //         'phone' => $request->phone,
    //         'nif' => $request->nif,
    //         'points' => 0,
    //     ]);
    //     $token = $user->createToken('token')->plainTextToken;
    //     return response()->json(['token' => $token], 200);
    // }

    // public function logout(Request $request)
    // {
    //     $request->user()->tokens()->delete();
    //     // dd($request->user()->currentAccessToken());
    //     // $request->user()->tokens()->where('id', auth()->id())->delete();
    //     // $request->user()->currentAccessToken()->delete();
    //     // return response(['message'=>'teste']);
        
    //     return response()->json(['message' => 'Logged out'], 200);
    // }

    public function userType(Request $request)
    {
        return response()->json(['type' => $request->user()->type], 200);
    }
}

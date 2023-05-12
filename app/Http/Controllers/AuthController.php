<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessfullySignUp;

use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register', 'resend', 'activate']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect credentials',
            ], 401);
        }

        $user = Auth::user();
        if ( ! $user->email_verified_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'The account is not activated',
            ], 401);
        }
 
Cookie::queue('name', 'value', 234234);

        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
    }

    public function me(Request $request) {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
        ]);
    }

    public function activate(Request $request) {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::where('email', $request->input('email'))->first();
        $user->email_verified_at = now();
        $user->save();

        return redirect($request->input('redirect'));
    }

    public function resend(Request $request) {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'redirect' => 'required'
        ]);

        try {
            $link =  url("/") . "/user/activate?email=" . $request->email . "&redirect=" . $request->input('redirect') . '/signup?email=' . $request->email;
            Mail::to($request->email)
                ->send(new SuccessfullySignUp($link, $request->email));
        } catch (\Exeption $e) {
            // ...
        }
    }

    public function register(Request $request) {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name ?? "",
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);

        try {
            $link =  url("/") . "/user/activate?email=" . $request->email . "&redirect=" . $request->input('redirect') . '/signup?email=' . $request->email;
            Mail::to($user)
                ->send(new SuccessfullySignUp($link, $request->email));
        } catch (\Exeption $e) {
            // ...
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}

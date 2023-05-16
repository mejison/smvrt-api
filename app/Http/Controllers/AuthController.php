<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessfullySignUp;
use App\Mail\ForgotPassword;

use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;
 
class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register', 'resend',
                                                     'activate', 'forgot', 'reset',
                                                     'google_redirect', 'callback_google']]);
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

    public function forgot(Request $request) {
        $request->validate([
            'email' => 'required|string|email|max:255|exists:users,email',
            'link' => 'required'
        ]);

        $link = $request->link . '/reset/' . md5($request->email);
        try {
            Mail::to($request->email)
                ->send(new ForgotPassword($link, $request->email));
        } catch (\Exception $e) {
            // ...
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully sent email for reset password',
        ]);
    }

    public function reset(Request $request) {
        $request->validate([
            'hash' => 'string',
            'email' => 'string|email',
            'password' => 'required|string|min:6'
        ]);

        if ($request->input('hash')) {
            $user = User::whereRaw('md5(email) = "' . $request->input('hash') . '"')->first();
        }

        if ($request->input('email')) {
            $user = User::where('email', $request->input('email'))->first();
        }

        if ( ! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!',
            ], 401);
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'The password successfully updated',
        ]);
    }

    public function google_redirect(Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl()
        ]);
    }

    public function callback_google(Request $request) {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::where('email', $googleUser->email)->first();
        if ( ! $user) {
            $user = User::create([
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'password' => bcrypt(time())
            ]);
            $user->save();
        }

        $token = Auth::login($user);
        return view('social-connect', ['token' => $token, 'user' => $user]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function update(Request $request) {
        $user = auth()->user();
        
        $request->validate([            
            'fname' => 'string',
            'lname' => 'string',
            'phone' => 'string',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
        ]);

        if($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('public/avatars');
        }

        if ( ! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found!',
            ], 401);
        }

       
        $user->fname = $request->input('fname');
        $user->lname = $request->input('lname');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->save();
        
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'message' => 'The profile successfully updated',
        ]);
    }
}

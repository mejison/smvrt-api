<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Models\TeamMember;

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

    public function update_settings(Request $request) {
        $user = auth()->user();

        $request->validate([          
            'setting' => ['string', 'required', Rule::in([
                    'assignee_changes', 
                    'status_cahnges', 
                    'tasks_assigned_to_me',
                    'document_edited',
                    'new_version_published',
                    'due_date_changes',
                    'due_date_overdue',
                    'before_due_date_reminder'
                    ])],
            'value' => 'required|boolean',
        ]);

        $setting = $request->input('setting');
        $value = $request->input('value');
        
        if ( ! $user->settings) {
            $default = [
                'assignee_changes' => false, 
                'status_cahnges' => false, 
                'tasks_assigned_to_me' => false,
                'document_edited' => false,
                'new_version_published' => false,
                'due_date_changes' => false,
                'due_date_overdue' => false,
                'before_due_date_reminder' => false
            ];

            $default[$setting] = $value;

            $user->settings()->create($default);
            $user->save();
        } else {
            $user->settings->{$setting} = $value;
            $user->settings->save();
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated'
        ]);
    }

    public function get_settings(Request $request) {
        $user = auth()->user();
        
        return response()->json([
            'status' => 'success',
            'data' => $user->settings,
        ]);
    }

    public function get_roles(Request $request) {
        $roles = Role::all();

        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }

    public function get_teams(Request $request) {
        $user = auth()->user();
        $teams = $user->teams()->get();

        $teams = $teams->map(function($team) {
            $team->members = TeamMember::with('user', 'role')->where('team_id', $team->id)->get();
            return $team;
        });

        return response()->json([
            'status' => 'success',
            'data' => $teams,
        ]);
    }
}

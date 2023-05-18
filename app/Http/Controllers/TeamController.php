<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\TeamMember;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function remove_member(Request $request, Team $team) {
        $request->validate([   
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
        ]]);

        $email = $request->input('email');

        $team->members()->wherePivot('email', $email)->detach();
        $targetMember = $team->members()->where('users.email', $email)->first();
        if ($targetMember) {
            $team->members()->detach($targetMember);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully deleted',
        ], 200);
    }

    public function member_update(Request $request, Team $team) {
        $request->validate([   
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'role' => [
                'required',
                'exists:roles,id'
            ]
        ]);

        TeamMember::where('team_id', $team->id)->where('email', $request->input('email'))
            ->update(['role_id' => $request->input('role')]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated',
        ], 200);
    }

    public function add_member(Request $request, Team $team) {
        $request->validate([   
            'members' => [
                'array'
            ]
        ]);

        $members = $request->input('members');
        foreach(collect($members) as $member) {
            $email = $member['email'];
            $role = $member['role']['value'];
            $name = $member['name'];
            
            $in_platform = User::where('email', $email)->count();
            if ($in_platform) {
                
                $exist =  $team->members()->where('users.email', $email)->count();
                if ($exist) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User already exist in team',
                    ], 401);
                } else {
                    $team->members()->attach($in_platform, ['role_id' => $role, 'name' => $name, 'email' => $email]);
                }
            } else {
                $exist = TeamMember::where('team_id', $team->id)->where('email', $email)->count();
                if ($exist) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User already exist in team',
                    ], 400);
                } else {
                    DB::table('team_members')->insert([
                        'name' => $name,
                        'email' => $email,
                        'role_id' => $role,
                        'team_id' => $team->id,
                        'user_id' => '0'
                    ]);
                }
        
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully added',
        ], 200);
    }

    public function create_team(Request $request) {
        $request->validate([ 
            'name' => [
                'required',
                'string'
            ],
            'members' => [
                'array'
            ]
        ]);

        $members = $request->input('members');

        $team = new Team();
        $team->name = $request->input('name');
        $team->save();

        $role = Role::get()->first();
        $user = auth()->user();

        $team->members()->attach($user, ['role_id' => $role->id, 
            'name' => ($user->fname && $user->lname ? $user->fname . " " . $user->lname : $user->email
        ) , 'email' => $user->email ]);

        if ( ! empty($members)) {
            collect($members)->each(function($member) use (&$team, $request) {
                $in_platform = User::where('email', $member['email'])->count();
                if ($in_platform) {
                    $exist =  $team->members()->where('users.email', $request->input('email'))->count();
                    if ($exist) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'User already exist in team',
                        ], 400);
                    }
                    
                    $team->members()->attach($in_platform, ['role_id' => $member['role']['value'], 'name' => $member['name'], 'email' => $member['email']]);
                } else {
                    DB::table('team_members')->insert([
                        'name' => $member['name'],
                        'email' => $member['email'],
                        'role_id' => $member['role']['value'],
                        'team_id' => $team->id,
                        'user_id' => '0'
                    ]);
                }
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created',
            'data' => $team,
        ], 201);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\TeamMember;
use App\Notifications\RequestsToChangeRole;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function requests_to_change_role(Request $request, Project $project, User $user) {
        $request->validate([   
            'role' => [
                'required',
                'exists:roles,id'
            ]
        ]);

        $team = $project->team;
        $lead = $project->team->members()->where('role_id', 1)->first();

        $roleTo = Role::find($request->input('role'));
        if ($lead) {
            $lead->notify(new RequestsToChangeRole($roleTo, $user, $team));
    
            $member = TeamMember::where('team_id', $team->id)->where('user_id', $user->id)->first();
            $roleFrom = Role::find($member->role_id);
            $leadName = $lead->fname && $lead->lname ? $lead->fname . ' ' . $lead->lname : $lead->email;

            return response()->json([
                'status' => 'success',
                'message' => 'Your request to udpate your role from "' . $roleFrom->name . '" to 
                            "' . $roleTo->name . '" was sent to the project lead ' . $leadName . '. You will be notified when your role is updated.',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead not found'
            ], 400);
        }
    }
}

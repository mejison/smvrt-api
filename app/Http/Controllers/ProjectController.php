<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
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
        $owner = $project->team->members()->where('role_id', 1)->first();

        $role = Role::find($request->input('role'));
        $owner->notify(new RequestsToChangeRole($role, $user, $team));

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully notified',
        ], 200);
    }
}

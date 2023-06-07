<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Document;
use App\Models\Category;
use App\Notifications\RequestsToChangeRole;
use Carbon\Carbon;
use App\Models\Approve;

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

    public function create(Request $request) {
        $user = auth()->user();

        $request->validate([   
            'name' => 'required|string',
            'duedate' => 'required',
            'notes' => '',
            'team' => '',
            'members' => '',
            
            'documentname' => 'required',
            'category' => 'required|integer',
            'type' => 'required|integer',
            'document' => 'required',

            'final_approver' => '',
            'approvers' => '',
            'save_for_future' => 'boolean'
        ]);

        $documentPath = $request->file('document')->store('/public/documents');

        $document = Document::create([
            'name' => $request->input('documentname'),
            'user_id' => $user->id,
            'type_id' => $request->input('type'),
            'category_id' => $request->input('category'),
            'path' => $documentPath,
        ]);

        $project = Project::create([
            'name' => $request->input('name'),
            'due_date' => Carbon::parse($request->input('duedate'))->format('Y-m-d'),
            'summary' => $request->input('notes'),
            'status' => 'new',
            'team_id' => ! empty($request->team)? json_decode($request->team)->id : 0,
            'document_id' => $document->id,
            'reminder_id' => $request->input('reminder_id') ?? 0,
        ]);

        if ( ! empty($request->approvers)) {
            $final_approver = $request->final_approver ? json_decode($request->final_approver) : false;
            
            collect($request->approvers)->each(function($approver) use ($final_approver, $document) {
                $approver = json_decode($approver);
                Approve::create([
                    'name' => $approver->name,
                    'email' => $approver->email,
                    'document_id' => $document->id,
                    'is_final' => $final_approver ? ($final_approver->value == $approver->email) : false
                ]);
            });
        }

        return response()->json([
            'data' => $project,
            'status' => 'success',
            'message' => "Successfully created"
        ]);
    }

    public function get_categories(Request $request) {
        $categories = Category::all();
        return response()->json([
            'data' => $categories,
        ]);
    }

    public function get(Request $request, Project $project) {
        return response()->json([
            'data' => $project->load(['document.type', 'document.category', 'team', 'team.members'])
        ]);
    }
}

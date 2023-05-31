<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\TeamMember;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function accept(Request $request, Notification $notification) {
        $user = auth()->user();

        if (in_array($notification->type, ['App\Notifications\RequestsToChangeRole'])) {
            $data = $notification->data;
            $target_user = $data->data->from ?? false;
            $target_role = $data->data->role ?? false;
            $target_team = $data->data->team ?? false;
            
            TeamMember::where('team_id', $target_team->id)->where('user_id', $target_user->id)
                ->update(['role_id' => $target_role->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully accepted',
            ], 200);
        }
    }

    public function reject(Request $request, Notification $notification) {

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully rejected',
        ], 200);
    }
}

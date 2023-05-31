<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
 

class RequestsToChangeRole extends Notification
{
    use Queueable;

    protected $role;
    protected $from;
    protected $team;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($role, $from, $team)
    {
        $this->role = $role;
        $this->from = $from;
        $this->team = $team;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'role' => $this->role,
            'from' => $this->from,
            'team' => $this->team,
            'message' => "asked to change his role to"
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return new BroadcastMessage([
            'role' => $this->role,
            'from' => $this->from,
            'team' => $this->team,
            'message' => "asked to change his role to"
        ]);
    }
}

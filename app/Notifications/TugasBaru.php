<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\SurveyorAssignment;

class TugasBaru extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SurveyorAssignment $assignment) {}
    public function via($notifiable)
    {
        return ['database']; 
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function toDatabase($notifiable): array
    {
        return [
            'assignment_id'   => $this->assignment->id,
            'alternative_name' => $this->assignment->alternative->name,
            'pesan'           => 'Anda mendapat tugas baru untuk melakukan penilaian terhadap alternatif: ' . $this->assignment->alternative->name,
            'assigned_at'     => $this->assignment->assigned_at,
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceRequest;

class MaintenanceAutoRepliedNotification extends Notification
{
    use Queueable;

    protected $maintenanceRequest;

    public function __construct(MaintenanceRequest $maintenanceRequest)
    {
        $this->maintenanceRequest = $maintenanceRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'maintenance_auto_reply',
            'title'   => '🤖 Assistance IA active',
            'message' => "L'IA a répondu à " . $this->maintenanceRequest->user->name . " sur : " . $this->maintenanceRequest->subject,
            'details' => $this->maintenanceRequest->message,
            'link'    => route('maintenance.index'),
        ];
    }
}

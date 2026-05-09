<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceRequest;

class MaintenanceNewRequestNotification extends Notification
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
            'type'    => 'maintenance_new_request',
            'title'   => '🛠️ Nouvelle demande de maintenance',
            'message' => $this->maintenanceRequest->user->name . " a envoyé une demande : " . $this->maintenanceRequest->subject,
            'details' => $this->maintenanceRequest->message,
            'link'    => route('incidents.index'),
        ];
    }
}

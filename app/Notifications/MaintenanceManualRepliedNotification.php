<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceRequest;

class MaintenanceManualRepliedNotification extends Notification
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
            'type'    => 'maintenance_manual_reply',
            'title'   => '✅ Réponse de l\'Admin',
            'message' => "L'administrateur a répondu manuellement à votre demande : " . $this->maintenanceRequest->subject,
            'details' => $this->maintenanceRequest->admin_response,
            'link'    => route('maintenance.index'),
        ];
    }
}

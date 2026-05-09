<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetByAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $newPassword;

    /**
     * Créer une nouvelle instance du mail.
     */
    public function __construct(User $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    /**
     * Définir l'enveloppe (objet) du mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Votre nouveau mot de passe GESTLOYER',
        );
    }

    /**
     * Définir le contenu du mail.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-by-admin',
        );
    }

    /**
     * Pièces jointes (aucune ici).
     */
    public function attachments(): array
    {
        return [];
    }
}

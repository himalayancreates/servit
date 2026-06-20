<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $inviteUrl;

    public function __construct(public readonly Invitation $invitation)
    {
        $this->inviteUrl = route('portal.invite', $invitation->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->invitation->email,
            subject: "You've been invited to set up your restaurant on ServIt",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }
}

<?php

namespace App\Mail;

use App\Models\StakingDeposit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StakingExpiringSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public StakingDeposit $stake,
        public int $daysLeft
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Staking is Expiring Soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staking.expiring',
        );
    }
}

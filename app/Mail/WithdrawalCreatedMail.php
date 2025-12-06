<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $amount;
    public string $walletAddress;
    public string $token;
    public string $network;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $userName,
        float $amount,
        string $walletAddress,
        string $token = 'USDT',
        string $network = 'tron'
    ) {
        $this->userName = $userName;
        $this->amount = number_format($amount, 2);
        $this->walletAddress = $walletAddress;
        $this->token = $token;
        $this->network = $network;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address('no-reply@lumastake.com', 'Lumastake'),
            subject: 'Withdrawal Request Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-created',
            with: [
                'userName' => $this->userName,
                'amount' => $this->amount,
                'walletAddress' => $this->walletAddress,
                'token' => $this->token,
                'network' => $this->network,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

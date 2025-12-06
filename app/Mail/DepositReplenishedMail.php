<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositReplenishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $amount;
    public string $token;
    public ?string $network;
    public ?string $networkLabel;

    public function __construct(string $userName, float $amount, string $token = 'USDT', ?string $network = null)
    {
        $this->userName = $userName;
        $this->amount = number_format($amount, 2);
        $this->token = $token;
        $this->network = $network;
        $this->networkLabel = $this->formatNetworkLabel($network);
    }

    private function formatNetworkLabel(?string $network): ?string
    {
        if (!$network) return null;
        return match(strtolower($network)) {
            'tron' => 'TRC-20',
            'ethereum', 'eth' => 'ERC-20',
            'bsc', 'bnb' => 'BEP-20',
            default => strtoupper($network),
        };
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address('no-reply@lumastake.com', 'Lumastake'),
            subject: 'Deposit Successful',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.deposit_replenished',
            with: [
                'userName' => $this->userName,
                'amount' => $this->amount,
                'token' => $this->token,
                'network' => $this->network,
                'networkLabel' => $this->networkLabel,
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

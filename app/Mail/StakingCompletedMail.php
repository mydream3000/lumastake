<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StakingCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public float $principalAmount;
    public float $profitAmount;
    public float $totalAmount;
    public int $days;
    public float $percentage;
    public bool $autoRenewal;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $userName,
        float $principalAmount,
        float $profitAmount,
        int $days,
        float $percentage,
        bool $autoRenewal = false
    ) {
        $this->userName = $userName;
        $this->principalAmount = $principalAmount;
        $this->profitAmount = $profitAmount;
        $this->totalAmount = $principalAmount + $profitAmount;
        $this->days = $days;
        $this->percentage = $percentage;
        $this->autoRenewal = $autoRenewal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address('no-reply@lumastake.com', 'Lumastake'),
            subject: 'Staking Completed Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.staking-completed',
            with: [
                'userName' => $this->userName,
                'principalAmount' => $this->principalAmount,
                'profitAmount' => $this->profitAmount,
                'totalAmount' => $this->totalAmount,
                'days' => $this->days,
                'percentage' => $this->percentage,
                'autoRenewal' => $this->autoRenewal,
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

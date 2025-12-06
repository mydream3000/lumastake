<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

class ContactReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $messagePreview;
    public $submittedAt;
    public $reference;
    private $template;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $userEmail, string $message, int $reference)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->messagePreview = mb_strlen($message) > 200
            ? mb_substr($message, 0, 200) . '...'
            : $message;
        $this->submittedAt = now()->format('F d, Y \\a\\t H:i');
        $this->reference = $reference;

        // Load template from database
        $this->template = EmailTemplate::getByKey('contact_received');

        Log::info('ContactReceivedMail template loaded', [
            'template_found' => $this->template !== null,
            'template_id' => $this->template?->id,
            'user_email' => $userEmail
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->template?->subject ?? 'We received your message - Lumastake Support';
        $senderName = $this->template?->sender_name ?? 'Arbitex Support Team';

        return new Envelope(
            subject: $subject,
            from: new Address(config('mail.from.address'), $senderName),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->getRenderedContent(),
        );
    }

    /**
     * Get the rendered HTML content
     */
    public function getRenderedContent(): string
    {
        if (!$this->template) {
            return $this->getDefaultContent();
        }

        // Replace template variables (with XSS protection)
        $content = $this->template->content;
        $content = str_replace('{{ $user_name }}', htmlspecialchars($this->userName, ENT_QUOTES, 'UTF-8'), $content);
        $content = str_replace('{{ $user_email }}', htmlspecialchars($this->userEmail, ENT_QUOTES, 'UTF-8'), $content);
        $content = str_replace('{{ $message_preview }}', htmlspecialchars($this->messagePreview, ENT_QUOTES, 'UTF-8'), $content);
        $content = str_replace('{{ $submitted_at }}', htmlspecialchars($this->submittedAt, ENT_QUOTES, 'UTF-8'), $content);
        $content = str_replace('{{ $reference }}', htmlspecialchars($this->reference, ENT_QUOTES, 'UTF-8'), $content);

        return $content;
    }

    /**
     * Get default content if template is not available
     */
    private function getDefaultContent(): string
    {
        $safeName = htmlspecialchars($this->userName, ENT_QUOTES, 'UTF-8');
        return "<p>Hello {$safeName},</p><p>Thank you for contacting us. We have received your message and will respond within 24-48 hours.</p>";
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

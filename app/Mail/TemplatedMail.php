<?php

namespace App\Mail;

use App\Models\EmailNotificationLog;
use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplatedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected EmailTemplate $template;
    protected EmailSetting $settings;
    protected array $data;
    protected ?int $userId;
    protected ?string $relatedType;
    protected ?int $relatedId;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $templateKey,
        array $data = [],
        ?int $userId = null,
        ?string $relatedType = null,
        ?int $relatedId = null
    ) {
        $this->template = EmailTemplate::getByKey($templateKey);

        if (!$this->template) {
            throw new \Exception("Email template '{$templateKey}' not found");
        }

        $this->settings = EmailSetting::getInstance();
        $this->data = $data;
        $this->userId = $userId;
        $this->relatedType = $relatedType;
        $this->relatedId = $relatedId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Use template sender_name if set, otherwise use global settings
        $senderName = $this->template->sender_name ?? $this->settings->sender_name;

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                $this->settings->sender_email,
                $senderName
            ),
            subject: $this->template->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Merge settings into data for footer
        $viewData = array_merge($this->data, [
            'supportEmail' => $this->settings->support_email,
            'footerText' => $this->settings->footer_text ?? '© ' . date('Y') . ' Lumastake. All rights reserved.',
            'footerSupport' => $this->settings->footer_support,
        ]);

        // Create temporary view from template content
        $viewName = 'emails.dynamic.' . $this->template->key;

        // Write content to temporary file in resources/views
        $path = resource_path('views/emails/dynamic');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents(
            $path . '/' . $this->template->key . '.blade.php',
            $this->template->content
        );

        return new Content(
            view: $viewName,
            with: $viewData,
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

    /**
     * Send the message and log it
     */
    public function send($mailer)
    {
        $result = parent::send($mailer);

        // Log the sent email if userId is provided
        if ($this->userId && $this->to[0]['address'] ?? null) {
            EmailNotificationLog::logSent(
                $this->userId,
                $this->template->key,
                $this->to[0]['address'],
                $this->template->subject,
                $this->relatedType,
                $this->relatedId
            );
        }

        return $result;
    }
}

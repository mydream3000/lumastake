<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class UpdateContactReceivedTemplate extends Command
{
    protected $signature = 'email:update-contact-received';
    protected $description = 'Update contact_received email template with reference variable';

    public function handle()
    {
        $this->info('Updating contact_received email template...');

        $template = EmailTemplate::where('key', 'contact_received')->first();

        if (!$template) {
            $this->error('Template not found! Run migration first.');
            return 1;
        }

        // Update variables list
        $variables = [
            'user_name' => 'Name of the user who submitted the form',
            'user_email' => 'Email address of the user',
            'message_preview' => 'Preview of the submitted message',
            'submitted_at' => 'Date and time when message was submitted',
            'reference' => 'Unique reference number for this message (6-digit number)'
        ];

        $template->variables = json_encode($variables);

        // Update content to use {{ $reference }} instead of rand()
        $content = $template->content;
        $content = str_replace(
            '#{{ rand(100000, 999999) }}',
            '#{{ $reference }}',
            $content
        );
        $template->content = $content;

        $template->save();

        $this->info('✅ Template updated successfully!');
        $this->info('Reference variable added to template.');

        return 0;
    }
}

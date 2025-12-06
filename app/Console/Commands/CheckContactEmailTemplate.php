<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class CheckContactEmailTemplate extends Command
{
    protected $signature = 'email:check-contact-template';
    protected $description = 'Check if contact_received email template exists and is enabled';

    public function handle()
    {
        $this->info('Checking contact_received email template...');

        $template = EmailTemplate::where('key', 'contact_received')->first();

        if (!$template) {
            $this->error('❌ Template NOT FOUND in database!');
            $this->warn('Run migration: php artisan migrate --path=database/migrations/2025_11_17_192412_add_contact_received_email_template.php');
            return 1;
        }

        $this->info('✅ Template found:');
        $this->table(
            ['ID', 'Key', 'Name', 'Enabled', 'Created'],
            [
                [
                    $template->id,
                    $template->key,
                    $template->name,
                    $template->enabled ? 'YES' : 'NO',
                    $template->created_at
                ]
            ]
        );

        if (!$template->enabled) {
            $this->error('❌ Template is DISABLED!');
            $this->warn('Enable it: UPDATE email_templates SET enabled = 1 WHERE key = "contact_received"');
            return 1;
        }

        $this->info('✅ Template is ENABLED and ready to use!');

        // Test template loading
        $testTemplate = EmailTemplate::getByKey('contact_received');
        if ($testTemplate) {
            $this->info('✅ getByKey() method works correctly');
        } else {
            $this->error('❌ getByKey() returned NULL!');
            return 1;
        }

        return 0;
    }
}

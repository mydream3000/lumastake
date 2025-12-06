<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use App\Models\ToastMessage;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $templates = EmailTemplate::orderBy('name')->get();

            return response()->json([
                'data' => $templates->map(function ($template) {
                    return [
                        'id' => $template->id,
                        'name' => $template->name,
                        'subject' => $template->subject,
                        'key' => $template->key,
                        'enabled' => $template->enabled ? 'Active' : 'Inactive',
                        'updated_at' => $template->updated_at->format('d, M, Y'),
                    ];
                }),
                'total' => $templates->count(),
                'per_page' => $templates->count(),
                'current_page' => 1,
                'last_page' => 1,
            ]);
        }

        return view('admin.email-templates.index');
    }

    /**
     * Show the form for creating a new template, optionally based on existing one
     */
    public function create(Request $request)
    {
        $fromId = $request->query('from');
        $source = null;
        if ($fromId) {
            $source = EmailTemplate::find($fromId);
        }

        // Prefill defaults
        $defaults = [
            'name' => $source?->name ? ($source->name . ' (Copy)') : '',
            'key' => $source?->key ? ($source->key . '_copy') : '',
            'subject' => $source->subject ?? '',
            'sender_name' => $source->sender_name ?? 'Lumastake',
            'content' => $source->content ?? '',
            'enabled' => true,
            'variables' => $source->variables ?? [],
            'from_id' => $source?->id,
        ];

        // Ensure suggested key is unique
        if (!empty($defaults['key'])) {
            $baseKey = $defaults['key'];
            $suffix = 1;
            while (EmailTemplate::where('key', $defaults['key'])->exists()) {
                $defaults['key'] = $baseKey . '_' . $suffix++;
            }
        }

        return view('admin.email-templates.create', [
            'defaults' => $defaults,
            'availableVariables' => (array) ($defaults['variables'] ?? []),
        ]);
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:email_templates,key',
            'subject' => 'required|string|max:255',
            'sender_name' => 'nullable|string|max:255',
            'content' => 'required|string',
            'enabled' => 'nullable|boolean',
            'from_id' => 'nullable|integer|exists:email_templates,id',
        ]);

        $validated['enabled'] = $request->has('enabled');

        // If created from existing, copy variables list
        $variables = null;
        if (!empty($validated['from_id'])) {
            $source = EmailTemplate::find($validated['from_id']);
            $variables = $source?->variables;
        }

        $template = EmailTemplate::create([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'subject' => $validated['subject'],
            'sender_name' => $validated['sender_name'] ?? 'Lumastake',
            'content' => $validated['content'],
            'variables' => $variables,
            'enabled' => $validated['enabled'],
        ]);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Email template successfully created',
            'type' => 'success',
        ]);

        return redirect()->route('admin.email-templates.edit', $template);
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', [
            'template' => $emailTemplate,
            'availableVariables' => (array) $emailTemplate->variables,
        ]);
    }

    /**
     * Update the specified template in database
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'sender_name' => 'nullable|string|max:255',
            'content' => 'required|string',
            'enabled' => 'nullable|boolean',
        ]);

        $validated['enabled'] = $request->has('enabled');

        $emailTemplate->update($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Email template successfully updated',
            'type' => 'success',
        ]);

        return redirect()->route('admin.email-templates.index');
    }

    /**
     * Show email settings page
     */
    public function settings()
    {
        $settings = EmailSetting::getInstance();
        return view('admin.email-templates.settings', compact('settings'));
    }

    /**
     * Update email settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'sender_email' => 'required|email|max:255',
            'sender_name' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'footer_text' => 'nullable|string',
            'footer_support' => 'nullable|boolean',
        ]);

        $validated['footer_support'] = $request->has('footer_support');

        $settings = EmailSetting::getInstance();
        $settings->update($validated);

        ToastMessage::create([
            'user_id' => auth()->id(),
            'message' => 'Email settings successfully updated',
            'type' => 'success',
        ]);

        return redirect()->route('admin.email-templates.settings');
    }

    /**
     * Send a test email
     */
    public function sendTest(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Generate test data based on template variables
            $testData = $this->generateTestData($emailTemplate);

            // Send immediately (not queued) for testing
            \Mail::to($request->test_email)->send(
                new \App\Mail\TemplatedMail(
                    $emailTemplate->key,
                    $testData
                )
            );

            // Log for debugging
            \Log::info('Test email sent', [
                'to' => $request->test_email,
                'template' => $emailTemplate->key,
                'mail_driver' => config('mail.default'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email,
            ]);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Failed to send test email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate test data for email template
     */
    private function generateTestData(EmailTemplate $template): array
    {
        $testData = [];
        $variables = (array) $template->variables;

        foreach ($variables as $key => $description) {
            $testData[$key] = match ($key) {
                'userName' => 'John Doe',
                'amount', 'principalAmount', 'profitAmount', 'totalAmount' => 1000.00,
                'code' => '123456',
                'days' => 30,
                'percentage' => 10.5,
                'autoRenewal' => true,
                'endDate' => now()->addDays(1)->format('M d, Y'),
                'reason' => 'Test rejection reason',
                default => 'Test Value',
            };
        }

        return $testData;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Mail\TemplatedMail;

class VeriffWebhookController extends Controller
{
    /**
     * Handle generic Veriff webhook events (session started, submitted, etc.)
     */
    public function events(Request $request)
    {
        $this->logIncoming('veriff.events', $request);

        if (!$this->verifySignature($request)) {
            Log::channel('veriff')->warning('[Veriff] Invalid signature for events webhook');
            // Depending on security policy, you might want to return a 403 here.
            // For now, we process it but log the warning.
        }

        // Best-effort parse of event payload to set Pending after submission
        try {
            $payload = $request->json()->all();
            $event = strtolower((string) (
                data_get($payload, 'event')
                ?? data_get($payload, 'type')
                ?? data_get($payload, 'status')
                ?? data_get($payload, 'action')
                ?? ''
            ));

            $user = $this->resolveUserFromVendorData($payload);

            if (in_array($event, ['submitted', 'submitted_retry', 'resubmission_requested', 'pending', 'review.started', 'review_pending'])) {
                if ($user) {
                    if ($user->verification_status !== 'pending') {
                        $old = $user->verification_status;
                        $user->verification_status = 'pending';
                        $user->save();
                        Log::channel('veriff')->info('[Veriff] User status set to pending from events webhook', [
                            'user_id' => $user->id,
                            'from' => $old,
                        ]);
                    }

                    // Send user-facing email on submission
                    if (in_array($event, ['submitted', 'submitted_retry'])) {
                        $this->sendEventEmail($user, 'submitted', $payload);
                    }
                }
            }

            // Send email when verification flow started
            if (in_array($event, ['started', 'start'])) {
                if ($user) {
                    $this->sendEventEmail($user, 'started', $payload);
                }
            }
        } catch (\Throwable $e) {
            Log::channel('veriff')->error('[Veriff] Failed to process events payload', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Veriff decisions webhook (approved/declined)
     */
    public function decisions(Request $request)
    {
        $this->logIncoming('veriff.decisions', $request);

        if (!$this->verifySignature($request)) {
            Log::channel('veriff')->warning('[Veriff] Invalid signature for decisions webhook');
            // Depending on security policy, you might want to return a 403 here.
        }

        // Try to map decision to user safely (best-effort, schema-agnostic)
        try {
            $payload = $request->json()->all();

            $decisionStatus = strtolower((string) (data_get($payload, 'decision.status') ?? data_get($payload, 'status')));
            $user = $this->resolveUserFromVendorData($payload);

            if ($user) {
                if (in_array($decisionStatus, ['approved', 'resubmission_requested', 'pending', 'declined', 'rejected'])) {
                    // Map to our enum states conservatively
                    $newStatus = match ($decisionStatus) {
                        'approved' => 'verified',
                        'pending', 'resubmission_requested' => 'pending',
                        default => 'unverified',
                    };

                    if ($user->verification_status !== $newStatus) {
                        $old = $user->verification_status;
                        $user->verification_status = $newStatus;
                        $user->save();
                        Log::channel('veriff')->info('[Veriff] User verification_status updated', [
                            'user_id' => $user->id,
                            'from' => $old,
                            'to' => $newStatus,
                        ]);
                    }

                    // Send notification emails on final decisions only
                    if (in_array($decisionStatus, ['approved', 'declined', 'rejected'])) {
                        $this->sendDecisionEmail($user, $decisionStatus, $payload);
                    }
                }
            } else {
                Log::channel('veriff')->warning('[Veriff] Decision received but user could not be resolved from vendorData', [
                    'vendorData' => data_get($payload, 'session.vendorData') ?? data_get($payload, 'vendorData') ?? data_get($payload, 'decision.vendorData')
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('veriff')->error('[Veriff] Failed to process decision payload', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function verifySignature(Request $request): bool
    {
        $signatureKey = Config::get('services.veriff.signature_key');
        if (empty($signatureKey)) {
            Log::channel('veriff')->notice('[Veriff] No signature key configured, skipping verification');
            return true;
        }

        // Trim the raw content to avoid issues with trailing whitespace
        $raw = trim($request->getContent());

        $provided = $request->header('X-Signature')
            ?? $request->header('X-Veriff-Signature')
            ?? '';

        if ($provided === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $raw, $signatureKey);
        $provided = strtolower(trim(str_replace('sha256=', '', $provided)));

        return hash_equals($expected, $provided);
    }

    private function logIncoming(string $channel, Request $request): void
    {
        Log::channel('veriff')->info('=== VERIFF WEBHOOK RECEIVED: ' . $channel . ' ===', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'query' => $request->query(),
            'json' => $request->json()->all(),
            'raw' => $request->getContent(),
        ]);
    }
    private function resolveUserFromVendorData(array $payload): ?User
    {
        $vendorData = data_get($payload, 'session.vendorData')
            ?? data_get($payload, 'vendorData')
            ?? data_get($payload, 'decision.vendorData');

        Log::channel('veriff')->info('[Veriff] Attempting to resolve user from vendorData', [
            'vendorData' => $vendorData
        ]);

        if (is_string($vendorData)) {
            $vendorData = trim($vendorData);
        }

        // If vendorData is array, try email or user_id
        if (is_array($vendorData)) {
            if (!empty($vendorData['email']) && is_string($vendorData['email'])) {
                return User::where('email', trim($vendorData['email']))->first();
            }
            if (!empty($vendorData['user_id']) || !empty($vendorData['userId'])) {
                $id = $vendorData['user_id'] ?? $vendorData['userId'];
                return User::find((int) $id);
            }
        }

        // If plain string, try to recognize as email or numeric id
        if (is_string($vendorData)) {
            if (filter_var($vendorData, FILTER_VALIDATE_EMAIL)) {
                return User::where('email', $vendorData)->first();
            }
        }
        if (is_numeric($vendorData)) {
            return User::find((int) $vendorData);
        }

        return null;
    }

    private function sendDecisionEmail(User $user, string $decisionStatus, array $payload): void
    {
        try {
            $templateKey = $decisionStatus === 'approved' ? 'kyc_approved' : 'kyc_declined';
            $template = EmailTemplate::getByKey($templateKey);
            if (!$template) {
                Log::channel('veriff')->notice('[Veriff] Email template not found or disabled', [
                    'key' => $templateKey,
                ]);
                return;
            }

            $data = [
                'userName' => $user->name ?? $user->email,
                'decision' => ucfirst($decisionStatus),
            ];

            Mail::to($user->email)->send(new TemplatedMail(
                $templateKey,
                $data,
                $user->id,
                'veriff_decision',
                null
            ));
        } catch (\Throwable $e) {
            Log::channel('veriff')->error('[Veriff] Failed to send decision email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendEventEmail(User $user, string $eventType, array $payload): void
    {
        try {
            $templateKey = match ($eventType) {
                'started' => 'kyc_started',
                'submitted' => 'kyc_submitted',
                default => null,
            };
            if (!$templateKey) {
                return;
            }

            $template = EmailTemplate::getByKey($templateKey);
            if (!$template) {
                Log::channel('veriff')->notice('[Veriff] Email template not found or disabled', [
                    'key' => $templateKey,
                ]);
                return;
            }

            $data = [
                'userName' => $user->name ?? $user->email,
            ];

            Mail::to($user->email)->send(new TemplatedMail(
                $templateKey,
                $data,
                $user->id,
                'veriff_event',
                null
            ));
        } catch (\Throwable $e) {
            Log::channel('veriff')->error('[Veriff] Failed to send event email', [
                'user_id' => $user->id,
                'event' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Veriff PEP & Sanctions webhook (optional AML alerts)
     */
    public function pepSanctions(\Illuminate\Http\Request $request)
    {
        $this->logIncoming('veriff.pep_sanctions', $request);

        if (!$this->verifySignature($request)) {
            Log::channel('veriff')->warning('[Veriff] Invalid signature for PEP & Sanctions webhook');
        }

        try {
            $payload = $request->json()->all();

            $user = $this->resolveUserFromVendorData($payload);

            // Minimal processing: log important parts; extend to persist flags if your model supports it
            Log::channel('veriff')->info('[Veriff] PEP & Sanctions payload processed', [
                'user_id' => $user?->id,
                'pep' => data_get($payload, 'pep') ?? data_get($payload, 'checks.pep'),
                'sanctions' => data_get($payload, 'sanctions') ?? data_get($payload, 'checks.sanctions'),
            ]);
        } catch (\Throwable $e) {
            Log::channel('veriff')->error('[Veriff] Failed to process PEP & Sanctions payload', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}

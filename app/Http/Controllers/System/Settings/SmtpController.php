<?php

namespace App\Http\Controllers\System\Settings;

use App\Http\Controllers\Controller;
use App\Models\System\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SmtpController extends Controller
{
    /**
     * Return the current SMTP settings for the SPA.
     */
    public function edit(): JsonResponse
    {
        return response()->json([
            'smtp_host' => Setting::where('key', 'smtp_host')->value('value') ?? '',
            'smtp_port' => Setting::where('key', 'smtp_port')->value('value') ?? '587',
            'smtp_encryption' => Setting::where('key', 'smtp_encryption')->value('value') ?? 'tls',
            'smtp_username' => Setting::where('key', 'smtp_username')->value('value') ?? '',
            'smtp_from_address' => Setting::where('key', 'smtp_from_address')->value('value') ?? '',
            'smtp_from_name' => Setting::where('key', 'smtp_from_name')->value('value') ?? config('app.name'),
        ]);
    }

    /**
     * Update SMTP settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_from_address' => 'required|email|max:255',
            'smtp_from_name' => 'required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'smtp_password' && empty($value)) {
                continue;
            }

            $settingValue = $key === 'smtp_password' ? encrypt($value) : $value;

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $settingValue]
            );
        }

        return response()->json(['message' => 'SMTP settings updated successfully.']);
    }

    /**
     * Send a test email.
     */
    public function test(Request $request): JsonResponse
    {
        try {
            $this->configureMailer();

            Mail::raw('This is a test email from your SMTP configuration.', function ($message) use ($request) {
                $message->to($request->user()->email)
                    ->subject('SMTP Test Email - '.config('app.name'));
            });

            return response()->json(['message' => 'Test email sent successfully to '.$request->user()->email]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send test email: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Configure the mailer with stored SMTP settings.
     */
    private function configureMailer(): void
    {
        $host = Setting::where('key', 'smtp_host')->value('value');
        $port = Setting::where('key', 'smtp_port')->value('value');
        $encryption = Setting::where('key', 'smtp_encryption')->value('value');
        $username = Setting::where('key', 'smtp_username')->value('value');
        $password = Setting::where('key', 'smtp_password')->value('value');
        $fromAddress = Setting::where('key', 'smtp_from_address')->value('value');
        $fromName = Setting::where('key', 'smtp_from_name')->value('value');

        if ($password) {
            $password = decrypt($password);
        }

        config([
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
        ]);
    }
}

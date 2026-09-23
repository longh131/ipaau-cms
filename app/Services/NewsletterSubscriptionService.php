<?php

namespace App\Services;

use App\Mail\NewsletterSubmissionMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscriptionService
{
    /**
     * @param  array{
     *     fullName: string,
     *     phone: string,
     *     email: string,
     *     company: string,
     *     jobTitle: string,
     *     education?: string|null
     * }  $data
     */
    public function subscribe(array $data): Subscriber
    {
        $subscriber = Subscriber::query()->create([
            'name' => $data['fullName'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'company' => filled($data['company'] ?? null) ? $data['company'] : null,
            'job_title' => filled($data['jobTitle'] ?? null) ? $data['jobTitle'] : null,
            'education' => filled($data['education'] ?? null) ? $data['education'] : null,
            'subscribed_at' => now(),
        ]);

        $recipient = app(SiteSettingsService::class)->getContactEmail()
            ?: config('newsletter.notification_to');

        if (filled($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($recipient)->send(new NewsletterSubmissionMail($subscriber));
            } catch (\Throwable $exception) {
                Log::error('Newsletter notification email failed.', [
                    'subscriber_id' => $subscriber->id,
                    'recipient' => $recipient,
                    'message' => $exception->getMessage(),
                ]);
            }
        } else {
            Log::warning('Newsletter submission stored but no valid contact email is configured.', [
                'subscriber_id' => $subscriber->id,
            ]);
        }

        return $subscriber;
    }
}

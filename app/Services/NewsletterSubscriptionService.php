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
     *     company?: string|null,
     *     jobTitle?: string|null,
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

        $recipient = config('newsletter.notification_to');

        if (filled($recipient)) {
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
            Log::warning('Newsletter submission stored but NEWSLETTER_TO is not configured.', [
                'subscriber_id' => $subscriber->id,
            ]);
        }

        return $subscriber;
    }
}

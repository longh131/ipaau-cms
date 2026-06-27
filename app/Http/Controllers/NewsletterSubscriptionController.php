<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Services\NewsletterSubscriptionService;
use Illuminate\Http\JsonResponse;

class NewsletterSubscriptionController extends Controller
{
    public function __construct(
        private readonly NewsletterSubscriptionService $newsletterSubscriptionService,
    ) {}

    public function store(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $this->newsletterSubscriptionService->subscribe($request->validated());

        return response()->json([
            'message' => '提交成功，感谢您的订阅。',
        ]);
    }
}

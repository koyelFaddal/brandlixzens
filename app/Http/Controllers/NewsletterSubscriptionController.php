<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));

        if (NewsletterSubscription::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            return response()->json([
                'message' => 'You have already subscribed with this email.',
            ], 409);
        }

        try {
            NewsletterSubscription::create(['email' => $email]);
        } catch (QueryException $exception) {
            if (in_array((string) $exception->getCode(), ['23000', '23505'], true)) {
                return response()->json([
                    'message' => 'You have already subscribed with this email.',
                ], 409);
            }

            throw $exception;
        }

        return response()->json([
            'message' => 'Thank you for subscription.',
        ], 201);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadSubmissionRequest;
use App\Models\LeadSubmission;
use App\Mail\LeadSubmissionReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadSubmissionController extends Controller
{
    public function captcha(): JsonResponse
    {
        $captcha = Str::random(6);
        $key = (string) Str::uuid();
        $captchas = session('lead_captchas', []);
        $captchas[$key] = $captcha;

        session(['lead_captchas' => array_slice($captchas, -10, null, true)]);

        return response()->json(['captcha' => $captcha, 'key' => $key]);
    }

    public function store(StoreLeadSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $receiverEmail = config('mail.receiver_email');

        abort_if(blank($receiverEmail), 500, 'RECEIVER_EMAIL is not configured.');

        $leadSubmission = LeadSubmission::create([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'city' => $validated['city'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'message' => $validated['message'] ?? null,
            'source_url' => $validated['source_url'] ?? url()->previous(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
        ]);

        Mail::to($receiverEmail)->send(new LeadSubmissionReceived($leadSubmission));

        return back()->with('lead_submission_success', 'Thank you. Your enquiry has been submitted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\JobApplicationReceived;
use App\Models\AiJob;
use App\Models\JobApplication;
use App\Services\ResumeStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        $jobs = AiJob::query()->latest('id')->get();

        return view('pages.about_us.careers', [
            'careerJobsPayload' => $jobs->map(fn (AiJob $job): array => [
                'id' => $job->id,
                'title' => $job->job_title,
                'description' => $job->overview,
                'tags' => array_values(array_filter([$job->work_mode, $job->work_location, $job->employment_type])),
                'category' => $job->category,
                'responsibilities' => $job->responsibilities,
                'required_skills' => $job->required_skills,
                'preferred_skills' => $job->preferred_skills,
                'experience_required' => $job->experience_required,
                'preview_image_url' => $job->preview_image ? asset('storage/'.$job->preview_image) : null,
            ])->values(),
        ]);
    }

    public function apply(Request $request, ResumeStorageService $resumeStorage): JsonResponse
    {
        $validated = $request->validate([
            'job_id' => ['required', 'exists:ai_jobs,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'major_experience' => ['required', 'string', 'max:255'],
            'present_salary' => ['required', 'string', 'max:100'],
            'expected_salary' => ['required', 'string', 'max:100'],
            'years_of_experience' => ['required', 'numeric', 'min:0', 'max:80'],
            'notice_period' => ['required', 'string', 'max:255'],
            'current_role' => ['required', 'string', 'max:255'],
            'skills' => ['required', 'string', 'max:5000'],
            'ai_challenge' => ['required', 'string', 'max:5000'],
            'resume' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120'],
        ]);

        $job = AiJob::findOrFail($validated['job_id']);
        $resume = $resumeStorage->store($request->file('resume'));

        $application = JobApplication::create([
            'job_id' => $validated['job_id'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'city' => $validated['city'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'major_experience' => $validated['major_experience'] ?? null,
            'years_of_experience' => $validated['years_of_experience'] ?? null,
            'present_salary' => $validated['present_salary'] ?? null,
            'expected_salary' => $validated['expected_salary'] ?? null,
            'notice_period' => $validated['notice_period'] ?? null,
            'current_role' => $validated['current_role'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'ai_challenge' => $validated['ai_challenge'] ?? null,
            ...$resume,
            'application_type' => 'job',
            'job_title_snapshot' => $job->job_title,
        ]);

        $receiver = config('mail.receiver_email');
        if ($receiver) {
            try {
                $resumeUrl = URL::temporarySignedRoute('careers.resume', now()->addDays(7), ['jobApplication' => $application->id]);
                Mail::to($receiver)->send(new JobApplicationReceived($application, $resumeUrl));
                $application->update(['email_notification_sent_at' => now()]);
            } catch (\Throwable $exception) {
                $application->update(['email_notification_error' => $exception->getMessage()]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Your application has been submitted successfully.',
        ], 201);
    }

    public function resume(JobApplication $jobApplication)
    {
        abort_unless(Storage::disk('local')->exists($jobApplication->resume_file_path), 404);
        return Storage::disk('local')->download($jobApplication->resume_file_path, $jobApplication->resume_file_name);
    }
}

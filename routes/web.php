<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\LeadSubmissionController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\CareerController;

use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdvancedCtaLeadController;
use App\Http\Controllers\Admin\AiJobController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BulkOrderController;
use App\Http\Controllers\Admin\BulkPurchaseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CtaLeadController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\DeliverySettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GiftOrderController;
use App\Http\Controllers\Admin\JiraTicketController;
use App\Http\Controllers\Admin\JobApplicationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\NewsletterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/partials/navbar-mobile', [PageController::class, 'navbarMobile']);
Route::get('/partials/navbar-desktop', [PageController::class, 'navbarDesktop']);
Route::get('/partials/footer', [PageController::class, 'footer']);

Route::get('/lead-form/captcha', [LeadSubmissionController::class, 'captcha'])
    ->middleware('throttle:30,1')
    ->name('lead-submissions.captcha');
Route::post('/lead-form', [LeadSubmissionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('lead-submissions.store');

Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('newsletter.subscribe');

Route::get('/about-us/careers', [CareerController::class, 'index'])->name('careers.index');
Route::post('/about-us/careers/apply', [CareerController::class, 'apply'])->middleware('throttle:5,1')->name('careers.apply');
Route::get('/career-applications/{jobApplication}/resume', [CareerController::class, 'resume'])->middleware('signed')->name('careers.resume');

$sections = [
    'about-us' => 'about_us',
    'advertising' => 'advertising',
    'branding' => 'branding',
    'design-development' => 'Design_Development',
    'industry' => 'industry',
    'marketing' => 'marketing',
    'optimization' => 'optimization',
];

foreach ($sections as $url => $viewFolder) {
    Route::get("/{$url}/{page}", [PageController::class, 'show'])
        ->defaults('section', $viewFolder)
        ->where('page', '[a-z0-9-]+')
        ->name("pages.{$url}");
}

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('change-password.update');
     Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
    Route::post('/newsletter/{newsletterSubscription}/send', [NewsletterController::class, 'send'])->name('newsletter.send');
    Route::get('/newsletter/{newsletterSubscription}/history', [NewsletterController::class, 'history'])->name('newsletter.history');
    Route::get('/newsletter/{newsletterSubscription}/history/{newsletterSentMail}', [NewsletterController::class, 'showMail'])->name('newsletter.history.show');
    Route::get('/newsletter/{newsletterSubscription}/history/{newsletterSentMail}/attachment', [NewsletterController::class, 'attachment'])->name('newsletter.history.attachment');

    Route::get('/career/jobs', [AdminPageController::class, 'jobs'])->name('career.jobs');
    Route::get('/career/jobs/data', [AiJobController::class, 'index'])->name('career.jobs.data');
    Route::post('/career/jobs', [AiJobController::class, 'store'])->name('career.jobs.store');
    Route::get('/career/jobs/{aiJob}', [AiJobController::class, 'show'])->name('career.jobs.show');
    Route::post('/career/jobs/{aiJob}', [AiJobController::class, 'update'])->name('career.jobs.update');
    Route::delete('/career/jobs/{aiJob}', [AiJobController::class, 'destroy'])->name('career.jobs.destroy');
    Route::get('/career/applications', [AdminPageController::class, 'applications'])->name('career.applications');
    Route::get('/career/applications/data', [JobApplicationController::class, 'index'])->name('career.applications.data');
    Route::post('/career/applications/resumes/download', [JobApplicationController::class, 'bulkResumeDownload'])->name('career.applications.resumes.download');
    Route::get('/career/applications/{jobApplication}', [JobApplicationController::class, 'show'])->name('career.applications.show');
    Route::get('/career/applications/{jobApplication}/resume', [JobApplicationController::class, 'resume'])->name('career.applications.resume');
    Route::delete('/career/applications/{jobApplication}', [JobApplicationController::class, 'destroy'])->name('career.applications.destroy');
});


Route::get('/fix-storage-link-brand-2026', function () {
    $token = request('token');


    $target = '/home4/brandixzen/brandxlen_backend/storage/app/public';
    $link = '/home4/brandixzen/public_html/storage';

    if (! is_dir($target)) {
        return response("Storage target does not exist: {$target}", 500)
            ->header('Content-Type', 'text/plain');
    }

    if (is_link($link)) {
        return response("Storage link already exists: {$link} -> " . readlink($link), 200)
            ->header('Content-Type', 'text/plain');
    }

    if (file_exists($link)) {
        return response("Path already exists but is not a symlink: {$link}", 409)
            ->header('Content-Type', 'text/plain');
    }

    if (! symlink($target, $link)) {
        return response("Failed to create storage link. Try running it from cPanel terminal.", 500)
            ->header('Content-Type', 'text/plain');
    }

    return response("Storage link created: {$link} -> {$target}", 200)
        ->header('Content-Type', 'text/plain');
});

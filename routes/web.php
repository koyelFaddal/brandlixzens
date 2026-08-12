<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\LeadSubmissionController;
use App\Http\Controllers\NewsletterSubscriptionController;

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
});

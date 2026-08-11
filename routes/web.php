<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\LeadSubmissionController;
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

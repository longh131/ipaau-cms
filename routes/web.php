<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Member\AuthController as MemberAuthController;
use App\Http\Controllers\Member\BolueSsoController;
use App\Http\Controllers\Member\CpdRecordsController;
use App\Http\Controllers\Member\PortalController as MemberPortalController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\SearchController;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('newsletter.subscribe');
Route::redirect('/home-exported.html', '/');
Route::get('/test-menu', function () {
    return view('frontend.test');
});
Route::get('/search', SearchController::class)->name('search');
Route::get('/page/{slug}', [FrontendController::class, 'render'])->name('page.show');
Route::get('/category/{slug}', [FrontendController::class, 'render'])->name('category.show');
Route::post('/category/{slug}/certificate-lookup', [\App\Http\Controllers\CertificateLookupController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('category.certificate-lookup');
Route::get('/article/{slug}', [FrontendController::class, 'render'])->name('article.show');
Route::get('/courses/{course}/register', [\App\Http\Controllers\CourseRegistrationController::class, 'redirect'])
    ->name('courses.register');

Route::prefix('member')->name('member.')->group(function (): void {
    Route::get('/login', [MemberAuthController::class, 'showLogin'])->name('login');
    Route::post('/send-code', [MemberAuthController::class, 'sendCode'])
        ->middleware('throttle:6,1')
        ->name('send-code');
    Route::post('/verify', [MemberAuthController::class, 'verify'])
        ->middleware('throttle:12,1')
        ->name('verify');
    Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout');

    Route::middleware('ipa.member')->group(function (): void {
        Route::get('/bolue-sso', BolueSsoController::class)
            ->middleware('throttle:10,1')
            ->name('bolue-sso');
        Route::get('/', [MemberPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [MemberPortalController::class, 'profile'])->name('profile');
        Route::post('/cpd-records/search', [CpdRecordsController::class, 'search'])
            ->middleware('throttle:30,1')
            ->name('cpd-records.search');
        Route::get('/cpd-records/print', [CpdRecordsController::class, 'print'])->name('cpd-records.print');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
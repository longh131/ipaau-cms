<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/test-menu', function () {
    return view('frontend.test');
});
Route::get('/page/{slug}', [FrontendController::class, 'render'])->name('page.show');
Route::get('/category/{slug}', [FrontendController::class, 'render'])->name('category.show');
Route::get('/article/{slug}', [FrontendController::class, 'render'])->name('article.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
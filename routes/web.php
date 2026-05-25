<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MemberController;

Route::get('/', [PageController::class, 'home']);
Route::get('/page/{slug}', [PageController::class, 'show']);
Route::get('/category/{slug}', [ArticleController::class, 'category']);
Route::get('/article/{slug}', [ArticleController::class, 'show']);
Route::get('/member/{id}', [MemberController::class, 'show']);
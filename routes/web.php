<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Models\MenuItem;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/test-menu', function() {
    return view('frontend.test');
});
Route::get('/page/{slug}', [FrontendController::class, 'render'])->name('page.show');
Route::get('/category/{slug}', [FrontendController::class, 'render'])->name('category.show');
Route::get('/article/{slug}', [FrontendController::class, 'render'])->name('article.show');

Route::post('/admin/menu-items/reorder', function () {
    $data = request()->validate([
        'dragged_id' => 'required|integer',
        'target_id' => 'required|integer',
    ]);

    $dragged = MenuItem::find($data['dragged_id']);
    $target = MenuItem::find($data['target_id']);

    if (!$dragged || !$target) {
        return response()->json(['success' => false, 'message' => 'Item not found']);
    }

    $dragged->parent_id = $target->parent_id;
    $dragged->sort_order = $target->sort_order;
    $dragged->save();

    $target->sort_order++;
    $target->save();

    return response()->json(['success' => true]);
})->middleware('auth');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
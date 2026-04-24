<?php

use App\Http\Controllers\Storefront\AccommodationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('demo/themes/{theme}', [AccommodationController::class, 'demoTheme'])
    ->name('storefront.demo-theme');

Route::get('preview/{accommodation:slug}', [AccommodationController::class, 'preview'])
    ->middleware('signed')
    ->name('storefront.preview');

Route::prefix('s')->group(function (): void {
    Route::get('{slug}', [AccommodationController::class, 'show'])->name('storefront.show');
    Route::post('{slug}/inquiry', [AccommodationController::class, 'storeInquiry'])->name('storefront.inquiry.store');
});

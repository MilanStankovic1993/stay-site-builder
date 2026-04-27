<?php

use App\Http\Controllers\Storefront\AccommodationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('locale/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, config('app.supported_locales', ['sr', 'en']), true), 404);

    session(['locale' => $locale]);

    $redirect = (string) $request->query('redirect', route('home'));
    $redirectHost = parse_url($redirect, PHP_URL_HOST);
    $requestHost = $request->getHost();

    if ($redirectHost && $redirectHost !== $requestHost) {
        $redirect = route('home');
    }

    return redirect()->to($redirect);
})->name('locale.switch');

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

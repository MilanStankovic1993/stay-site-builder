<?php

use App\Http\Controllers\Storefront\AccommodationController;
use App\Http\Controllers\OwnerBillingController;
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

Route::middleware('auth')->prefix('dashboard/billing')->group(function (): void {
    Route::get('/', [OwnerBillingController::class, 'index'])->name('dashboard.billing');
    Route::post('update-payment-method', [OwnerBillingController::class, 'updatePaymentMethod'])->name('dashboard.billing.update-payment-method');
    Route::post('change-plan/{plan}', [OwnerBillingController::class, 'changePlan'])->name('dashboard.billing.change-plan');
    Route::post('cancel', [OwnerBillingController::class, 'cancel'])->name('dashboard.billing.cancel');
    Route::post('resume', [OwnerBillingController::class, 'resume'])->name('dashboard.billing.resume');
    Route::get('{plan}', [OwnerBillingController::class, 'checkout'])->name('dashboard.billing.checkout');
});

Route::get('demo/themes/{theme}', [AccommodationController::class, 'demoTheme'])
    ->name('storefront.demo-theme');

Route::get('preview/{accommodation:slug}', [AccommodationController::class, 'preview'])
    ->middleware('signed')
    ->name('storefront.preview');

Route::prefix('s')->group(function (): void {
    Route::get('{slug}', [AccommodationController::class, 'show'])->name('storefront.show');
    Route::post('{slug}/inquiry', [AccommodationController::class, 'storeInquiry'])->name('storefront.inquiry.store');
});

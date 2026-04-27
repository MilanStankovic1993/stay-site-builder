<?php

namespace App\Models;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Accommodation extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title',
        'title_en',
        'slug',
        'type',
        'status',
        'short_description',
        'short_description_en',
        'description',
        'description_en',
        'location_name',
        'location_name_en',
        'address',
        'address_en',
        'city',
        'city_en',
        'region',
        'region_en',
        'country',
        'country_en',
        'latitude',
        'longitude',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'beds',
        'size_m2',
        'price_from',
        'currency',
        'contact_name',
        'contact_phone',
        'contact_email',
        'whatsapp_number',
        'viber_number',
        'instagram_url',
        'facebook_url',
        'booking_url',
        'airbnb_url',
        'website_url',
        'google_maps_url',
        'theme_key',
        'primary_color',
        'secondary_color',
        'meta_title',
        'meta_title_en',
        'meta_description',
        'meta_description_en',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AccommodationType::class,
            'status' => AccommodationStatus::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'price_from' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class)->withTimestamps();
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(AccommodationInquiry::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', AccommodationStatus::Published);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')->singleFile();
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('logo')->singleFile();
    }

    public function themeView(): string
    {
        $theme = $this->theme_key ?: app(\App\Settings\PlatformSettings::class)->default_theme;
        $theme = view()->exists("storefront.themes.{$theme}.show") ? $theme : 'default';

        return "storefront.themes.{$theme}.show";
    }

    public function publicUrl(): string
    {
        return route('storefront.show', $this->slug);
    }

    public function previewUrl(): string
    {
        return URL::temporarySignedRoute(
            'storefront.preview',
            now()->addMinutes(120),
            ['accommodation' => $this->slug],
        );
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('hero') ?: $this->getFirstMediaUrl('gallery');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function getDisplayTitleAttribute(): string
    {
        return (string) $this->localizedValue('title');
    }

    public function getDisplayShortDescriptionAttribute(): ?string
    {
        return $this->localizedValue('short_description');
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        return $this->localizedValue('description');
    }

    public function getDisplayLocationNameAttribute(): ?string
    {
        return $this->localizedValue('location_name');
    }

    public function getDisplayAddressAttribute(): ?string
    {
        return $this->localizedValue('address');
    }

    public function getDisplayCityAttribute(): ?string
    {
        return $this->localizedValue('city');
    }

    public function getDisplayRegionAttribute(): ?string
    {
        return $this->localizedValue('region');
    }

    public function getDisplayCountryAttribute(): ?string
    {
        return $this->localizedValue('country');
    }

    public function getDisplayMetaTitleAttribute(): ?string
    {
        return $this->localizedValue('meta_title');
    }

    public function getDisplayMetaDescriptionAttribute(): ?string
    {
        return $this->localizedValue('meta_description');
    }

    public function localizedValue(string $attribute): mixed
    {
        $localizedAttribute = $attribute.'_en';

        if (app()->getLocale() === 'en' && filled($this->{$localizedAttribute} ?? null)) {
            return $this->{$localizedAttribute};
        }

        return $this->{$attribute};
    }

    public function isDemoAccommodation(): bool
    {
        return $this->slug === 'villa-lavanda-tara'
            || $this->title === 'Villa Lavanda Tara'
            || $this->user?->isDemoAccount();
    }
}

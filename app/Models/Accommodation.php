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
        'slug',
        'type',
        'status',
        'short_description',
        'description',
        'location_name',
        'address',
        'city',
        'region',
        'country',
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
        'meta_description',
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

    public function isDemoAccommodation(): bool
    {
        return $this->slug === 'villa-lavanda-tara'
            || $this->title === 'Villa Lavanda Tara'
            || $this->user?->isDemoAccount();
    }
}

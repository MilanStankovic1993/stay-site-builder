<?php

namespace Database\Seeders;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Amenity;
use App\Models\ThemePreset;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'can_publish_sites' => true,
            ],
        );
        $admin->syncRoles([$superAdminRole]);

        $owner = User::query()->updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Demo Owner',
                'password' => Hash::make('password'),
                'is_active' => true,
                'can_publish_sites' => true,
            ],
        );
        $owner->syncRoles([$ownerRole]);

        collect([
            ['name' => 'WiFi', 'category' => 'Osnovno'],
            ['name' => 'Parking', 'category' => 'Osnovno'],
            ['name' => 'Klima', 'category' => 'Komfor'],
            ['name' => 'Bazen', 'category' => 'Wellness'],
            ['name' => 'Terasa', 'category' => 'Eksterijer'],
            ['name' => 'Kuhinja', 'category' => 'Osnovno'],
            ['name' => 'Kamin', 'category' => 'Atmosfera'],
            ['name' => 'Pet friendly', 'category' => 'Dodatno'],
            ['name' => 'Pogled na planinu', 'category' => 'Pogled'],
            ['name' => 'Sauna', 'category' => 'Wellness'],
            ['name' => 'Jacuzzi', 'category' => 'Wellness'],
        ])->each(fn (array $amenity) => Amenity::query()->firstOrCreate(['name' => $amenity['name']], $amenity));

        collect([
            [
                'key' => 'default',
                'name' => 'Default',
                'description' => 'Elegantna i univerzalna premium tema za apartmane, vile i kuce za odmor.',
                'preview_image' => url('/demo/placeholders/hero-villa.svg'),
                'is_active' => true,
            ],
            [
                'key' => 'luxury',
                'name' => 'Luxury',
                'description' => 'Editorial i sofisticirana tema za luksuzne vile i objekte sa jacim premium identitetom.',
                'preview_image' => url('/demo/placeholders/gallery-lounge.svg'),
                'is_active' => true,
            ],
            [
                'key' => 'nature',
                'name' => 'Nature',
                'description' => 'Topla i moderna tema za brvnare, vikendice i smestaje u prirodi.',
                'preview_image' => url('/demo/placeholders/gallery-bedroom.svg'),
                'is_active' => true,
            ],
        ])->each(fn (array $theme) => ThemePreset::query()->updateOrCreate(
            ['key' => $theme['key']],
            $theme,
        ));

        $accommodation = Accommodation::query()->updateOrCreate(
            ['slug' => 'villa-lavanda-tara'],
            [
                'user_id' => $owner->id,
                'title' => 'Villa Lavanda Tara',
                'title_en' => 'Lavanda Tara Villa',
                'type' => AccommodationType::Villa,
                'status' => AccommodationStatus::Published,
                'short_description' => 'Premium vila sa panoramskim pogledom i privatnim wellness kutkom.',
                'short_description_en' => 'A premium villa with panoramic views and a private wellness corner.',
                'description' => 'Villa Lavanda Tara je pazljivo uredjena vila za porodicni odmor i vikend beg iz grada. Prostrani dnevni boravak, velika terasa i pazljivo birani detalji daju osecaj boutique smestaja sa toplinom doma.',
                'description_en' => 'Lavanda Tara Villa is a carefully designed retreat for family holidays and weekend escapes. A spacious living area, large terrace and refined details create a boutique hospitality feel with the warmth of home.',
                'location_name' => 'Planina Tara',
                'location_name_en' => 'Tara Mountain',
                'address' => 'Zaovinsko jezero bb',
                'address_en' => 'Zaovine Lake bb',
                'city' => 'Bajina Basta',
                'city_en' => 'Bajina Basta',
                'region' => 'Zapadna Srbija',
                'region_en' => 'Western Serbia',
                'country' => 'Srbija',
                'country_en' => 'Serbia',
                'latitude' => 43.9034500,
                'longitude' => 19.4512200,
                'max_guests' => 6,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'beds' => 4,
                'size_m2' => 110,
                'price_from' => 140,
                'currency' => 'EUR',
                'contact_name' => 'Milica Jovanovic',
                'contact_phone' => '+38160111222',
                'contact_email' => 'owner@example.com',
                'whatsapp_number' => '+38160111222',
                'instagram_url' => 'https://instagram.com/staysitebuilder',
                'facebook_url' => 'https://facebook.com/staysitebuilder',
                'booking_url' => 'https://booking.example.com/villa-lavanda-tara',
                'airbnb_url' => 'https://airbnb.example.com/villa-lavanda-tara',
                'google_maps_url' => 'https://maps.google.com/?q=43.90345,19.45122',
                'theme_key' => 'default',
                'primary_color' => '#2C4A3E',
                'secondary_color' => '#C6A66B',
                'meta_title' => 'Villa Lavanda Tara | Premium smestaj na Tari',
                'meta_title_en' => 'Lavanda Tara Villa | Premium stay on Tara Mountain',
                'meta_description' => 'Otkrijte mir, pogled i toplinu premium vile na Tari. Posaljite upit direktno vlasniku.',
                'meta_description_en' => 'Discover the calm, views and warmth of a premium villa on Tara Mountain. Send an inquiry directly to the owner.',
                'published_at' => now(),
            ],
        );

        $amenityIds = Amenity::query()
            ->whereIn('name', ['WiFi', 'Parking', 'Terasa', 'Kuhinja', 'Sauna', 'Jacuzzi', 'Pogled na planinu'])
            ->pluck('id');

        $accommodation->amenities()->sync($amenityIds);

        $heroPath = public_path('demo/placeholders/hero-villa.svg');
        $galleryPaths = [
            public_path('demo/placeholders/gallery-lounge.svg'),
            public_path('demo/placeholders/gallery-bedroom.svg'),
            public_path('demo/placeholders/gallery-wellness.svg'),
        ];

        if (file_exists($heroPath) && ! $accommodation->getFirstMedia('hero')) {
            $accommodation->addMedia($heroPath)->preservingOriginal()->toMediaCollection('hero', 'public');
        }

        if (! $accommodation->getMedia('gallery')->count()) {
            foreach ($galleryPaths as $path) {
                if (file_exists($path)) {
                    $accommodation->addMedia($path)->preservingOriginal()->toMediaCollection('gallery', 'public');
                }
            }
        }

        $staffRole->touch();
    }
}

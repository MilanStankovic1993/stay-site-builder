<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 50);
            $table->string('status', 50)->default('draft');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('location_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable()->index();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('max_guests')->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('beds')->nullable();
            $table->unsignedInteger('size_m2')->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->char('currency', 3)->default('EUR');
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('viber_number')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('booking_url')->nullable();
            $table->string('airbnb_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('theme_key')->default('default');
            $table->string('primary_color', 20)->nullable();
            $table->string('secondary_color', 20)->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};

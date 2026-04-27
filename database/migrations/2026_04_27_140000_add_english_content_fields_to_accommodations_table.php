<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->string('title_en')->nullable()->after('title');
            $table->text('short_description_en')->nullable()->after('short_description');
            $table->longText('description_en')->nullable()->after('description');
            $table->string('location_name_en')->nullable()->after('location_name');
            $table->string('address_en')->nullable()->after('address');
            $table->string('city_en')->nullable()->after('city');
            $table->string('region_en')->nullable()->after('region');
            $table->string('country_en')->nullable()->after('country');
            $table->string('meta_title_en')->nullable()->after('meta_title');
            $table->text('meta_description_en')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->dropColumn([
                'title_en',
                'short_description_en',
                'description_en',
                'location_name_en',
                'address_en',
                'city_en',
                'region_en',
                'country_en',
                'meta_title_en',
                'meta_description_en',
            ]);
        });
    }
};

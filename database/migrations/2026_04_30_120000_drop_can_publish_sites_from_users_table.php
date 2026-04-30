<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'can_publish_sites')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('can_publish_sites');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'can_publish_sites')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('can_publish_sites')->default(false)->after('is_active');
        });
    }
};

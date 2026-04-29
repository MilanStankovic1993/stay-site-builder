<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('manual_billing_plan_key')->nullable()->after('can_publish_sites');
            $table->timestamp('manual_billing_activated_at')->nullable()->after('manual_billing_plan_key');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['manual_billing_plan_key', 'manual_billing_activated_at']);
        });
    }
};

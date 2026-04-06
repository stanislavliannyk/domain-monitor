<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name')->comment('Human-readable label');
            $table->string('url')->comment('Full URL including scheme, e.g. https://example.com');

            // Check settings
            $table->unsignedSmallInteger('check_interval')->default(5)->comment('Minutes between checks');
            $table->unsignedSmallInteger('request_timeout')->default(10)->comment('HTTP timeout in seconds');
            $table->enum('check_method', ['GET', 'HEAD'])->default('HEAD');

            // State
            $table->boolean('is_active')->default(true)->index();
            $table->enum('status', ['unknown', 'up', 'down'])->default('unknown')->index();
            $table->timestamp('last_checked_at')->nullable()->index();

            // Notifications (bonus)
            $table->boolean('notify_on_failure')->default(false);
            $table->string('notification_email')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['is_active', 'last_checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};

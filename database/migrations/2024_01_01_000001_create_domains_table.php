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

            $table->string('name')->comment('Читаемое название домена');
            $table->string('url')->comment('Полный URL, включая схему, например https://example.com');

            $table->unsignedSmallInteger('check_interval')->default(5)->comment('Интервал между проверками в минутах');
            $table->unsignedSmallInteger('request_timeout')->default(10)->comment('Таймаут HTTP-запроса в секундах');
            $table->enum('check_method', ['GET', 'HEAD'])->default('HEAD');

            $table->boolean('is_active')->default(true)->index();
            $table->enum('status', ['unknown', 'up', 'down'])->default('unknown')->index();
            $table->timestamp('last_checked_at')->nullable()->index();

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

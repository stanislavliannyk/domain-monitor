<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();

            $table->timestamp('checked_at')->index();
            $table->boolean('is_up');
            $table->unsignedSmallInteger('http_code')->nullable()->comment('HTTP response code');
            $table->unsignedInteger('response_time_ms')->nullable()->comment('Response time in milliseconds');
            $table->text('error_message')->nullable();

            $table->index(['domain_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_logs');
    }
};

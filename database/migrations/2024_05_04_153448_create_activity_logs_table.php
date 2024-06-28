<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->unsignedBigInteger('performed_by')->nullable();
        $table->unsignedBigInteger('event_type_id');
        $table->string('ip_address', 45)->nullable();
        $table->timestamp('created_at');
    
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');
        $table->foreign('event_type_id')->references('id')->on('event_types');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

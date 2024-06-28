<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->text('message')->nullable()->after('ip_address');
            $table->string('email')->nullable()->after('performed_by');; 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('message');
            $table->dropColumn('email');
        });
    }
};

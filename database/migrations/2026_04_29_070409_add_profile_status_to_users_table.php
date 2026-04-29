<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_add_profile_status_to_users_table.php
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // We use boolean with a default of false
            $table->boolean('is_profile_completed')->default(false)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_profile_completed');
        });
    }
};

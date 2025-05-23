<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('calorie_goal_type')->nullable()->after('target_weight');
            $table->float('calorie_goal')->nullable()->after('calorie_goal_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['calorie_goal_type', 'calorie_goal']);
        });
    }
};
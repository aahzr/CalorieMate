<?php
// database/migrations/YYYY_MM_DD_create_food_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodLogsTable extends Migration
{
    public function up()
    {
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('meal_type'); // Breakfast, Lunch, Dinner, Snack
            $table->string('food_name');
            $table->integer('calories');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('food_logs');
    }
}
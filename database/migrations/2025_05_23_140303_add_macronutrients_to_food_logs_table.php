<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMacronutrientsToFoodLogsTable extends Migration
{
    public function up()
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->float('carbohydrate')->nullable()->after('calories');
            $table->float('protein')->nullable()->after('carbohydrate');
            $table->float('fat')->nullable()->after('protein');
        });
    }

    public function down()
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->dropColumn(['carbohydrate', 'protein', 'fat']);
        });
    }
}
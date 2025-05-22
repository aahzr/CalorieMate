<?php
// app/Http/Controllers/ReportController.php
namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $calorieData = FoodLog::where('user_id', auth()->id())
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->groupBy('date')
            ->selectRaw('date, SUM(calories) as total_calories')
            ->get();
        $weightData = WeightLog::where('user_id', auth()->id())
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->orderBy('date')
            ->get();
        $favoriteFoods = FoodLog::where('user_id', auth()->id())
            ->groupBy('food_name')
            ->selectRaw('food_name, COUNT(*) as count, SUM(calories) as total_calories')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        return view('reports', compact('calorieData', 'weightData', 'favoriteFoods'));
    }
}
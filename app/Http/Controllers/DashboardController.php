<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $caloriesToday = FoodLog::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->sum('calories');
        $streak = 5; // Ganti dengan logika perhitungan streak
        $caloriesBurned = 420; // Ganti dengan logika perhitungan kalori terbakar
        $calorieGoal = 1800;

        return view('dashboard', compact('caloriesToday', 'streak', 'caloriesBurned', 'calorieGoal'));
    }
}
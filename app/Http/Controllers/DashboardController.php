<?php
namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $date = Carbon::today(); // Selalu objek Carbon
        $caloriesToday = FoodLog::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->sum('calories');
        $calorieGoal = Auth::user()->calorie_goal ?? 1800; // Sinkron dengan FoodLogController
        $remainingCalories = max(0, $calorieGoal - $caloriesToday);

        // Logika placeholder untuk streak
        $streak = FoodLog::where('user_id', auth()->id())
            ->where('date', '>=', Carbon::today()->subDays(30))
            ->orderBy('date', 'asc') // Urutkan untuk konsistensi
            ->get()
            ->groupBy(function ($log) {
                return Carbon::parse($log->date)->format('Y-m-d'); // Konversi string ke Carbon
            })
            ->reduce(function ($streak, $logs, $date) {
                static $lastDate = null;
                $currentDate = Carbon::parse($date);
                if ($lastDate === null) {
                    $lastDate = $currentDate;
                    return 1;
                }
                if ($lastDate->subDay()->eq($currentDate)) {
                    $lastDate = $currentDate;
                    return $streak + 1;
                }
                return $streak;
            }, 0) ?: 5; // Default 5 sesuai kode asli

        // Logika placeholder untuk kalori terbakar
        $weightLog = WeightLog::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->first();
        $caloriesBurned = $weightLog ? round($weightLog->weight * 5.5) : 420; // 5.5 kcal/kg/hari, default 420

        \Log::info('Dashboard: date=' . $date->format('Y-m-d') . ', calories=' . $caloriesToday . ', goal=' . $calorieGoal . ', remaining=' . $remainingCalories . ', streak=' . $streak . ', burned=' . $caloriesBurned);

        return view('dashboard', compact('caloriesToday', 'calorieGoal', 'remainingCalories', 'streak', 'caloriesBurned', 'date'));
    }
}
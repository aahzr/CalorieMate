<?php
namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\WeightLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $calorieGoalType = $user->calorie_goal_type ?? 'deficit';
        $targetWeight = $user->target_weight ?? null;

        // Data Kalori Mingguan untuk Chart
        $calorieData = FoodLog::where('user_id', auth()->id())
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->groupBy('date')
            ->selectRaw('date, SUM(calories) as total_calories')
            ->get();

        // Data Berat Badan untuk Chart
        $weightData = WeightLog::where('user_id', auth()->id())
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->orderBy('date')
            ->get();

        // Makanan Favorit
        $favoriteFoods = FoodLog::where('user_id', auth()->id())
            ->groupBy('food_name')
            ->selectRaw('food_name, COUNT(*) as count, SUM(calories) as total_calories')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        // Hitung Streak (Hari Terbaik Berturut-turut)
        $streak = 0;
        $currentDate = Carbon::today();
        while (WeightLog::where('user_id', auth()->id())->whereDate('date', $currentDate)->exists()) {
            $streak++;
            $currentDate->subDay();
        }

        // Hitung Hari Catatan Lengkap (berdasarkan FoodLog)
        $completeDays = FoodLog::where('user_id', auth()->id())
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->select('date')
            ->groupBy('date')
            ->distinct()
            ->count();

        // Hitung Perubahan Berat Badan
        $weightChange = null;
        if ($weightData->count() > 1) {
            $firstWeight = $weightData->first()->weight;
            $lastWeight = $weightData->last()->weight;
            $weightChange = $lastWeight - $firstWeight;
        }

        // Hitung Capaian Target Mingguan
        $calorieTargetDays = 6; // Target 6 dari 7 hari
        $weightTarget = 0.5; // Target 0.5 kg per minggu
        $loggedCalorieDays = $calorieData->count();
        $actualWeightChange = $weightChange ?? 0;

        $calorieTargetProgress = min(100, ($loggedCalorieDays / $calorieTargetDays) * 100);
        $weightTargetProgress = $calorieGoalType == 'deficit' 
            ? min(100, (max(0, $weightTarget - abs($actualWeightChange)) / $weightTarget) * 100)
            : min(100, (max(0, abs($actualWeightChange)) / $weightTarget) * 100);
        $foodLogProgress = min(100, ($completeDays / 7) * 100);

        return view('reports', compact('calorieData', 'weightData', 'favoriteFoods', 'streak', 'completeDays', 'weightChange', 'calorieGoalType', 'calorieTargetProgress', 'weightTargetProgress', 'foodLogProgress'));
    }

    public function exportPdf()
    {
        $user = Auth::user();
        $calorieGoalType = $user->calorie_goal_type ?? 'deficit';
        $targetWeight = $user->target_weight ?? null;

        // Data untuk PDF
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

        $streak = 0;
        $currentDate = Carbon::today();
        while (WeightLog::where('user_id', auth()->id())->whereDate('date', $currentDate)->exists()) {
            $streak++;
            $currentDate->subDay();
        }

        $completeDays = FoodLog::where('user_id', auth()->id())
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->select('date')
            ->groupBy('date')
            ->distinct()
            ->count();

        $weightChange = null;
        if ($weightData->count() > 1) {
            $firstWeight = $weightData->first()->weight;
            $lastWeight = $weightData->last()->weight;
            $weightChange = $lastWeight - $firstWeight;
        }

        $calorieTargetDays = 6;
        $weightTarget = 0.5;
        $loggedCalorieDays = $calorieData->count();
        $actualWeightChange = $weightChange ?? 0;

        $calorieTargetProgress = min(100, ($loggedCalorieDays / $calorieTargetDays) * 100);
        $weightTargetProgress = $calorieGoalType == 'deficit' 
            ? min(100, (max(0, $weightTarget - abs($actualWeightChange)) / $weightTarget) * 100)
            : min(100, (max(0, abs($actualWeightChange)) / $weightTarget) * 100);
        $foodLogProgress = min(100, ($completeDays / 7) * 100);

        $reportDate = Carbon::now()->format('d M Y, H:i');
        $pdf = Pdf::loadView('reports.pdf', compact('calorieData', 'weightData', 'favoriteFoods', 'streak', 'completeDays', 'weightChange', 'calorieGoalType', 'calorieTargetProgress', 'weightTargetProgress', 'foodLogProgress', 'reportDate'));

        return $pdf->download('report_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}
<?php
namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FoodLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date'))->startOfDay() : Carbon::today();
        $foodLogs = FoodLog::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->get()
            ->groupBy('meal_type');
        $caloriesToday = $foodLogs->flatten()->sum('calories');
        $calorieGoal = Auth::user()->calorie_goal ?? 1800;
        $remainingCalories = max(0, $calorieGoal - $caloriesToday);

        \Log::info('FoodLog: date=' . $date->format('Y-m-d') . ', calories=' . $caloriesToday . ', goal=' . $calorieGoal . ', remaining=' . $remainingCalories);

        return view('food-log', compact('foodLogs', 'caloriesToday', 'calorieGoal', 'remainingCalories', 'date'));
    }

    public function store(Request $request)
    {
        $date = Carbon::parse($request->query('date', Carbon::today()))->startOfDay();
        $request->validate([
            'meal_type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
            'food_name' => 'required|string|max:255',
            'calories' => 'required|integer|min:0',
        ]);

        FoodLog::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'meal_type' => $request->meal_type,
            'food_name' => $request->food_name,
            'calories' => $request->calories,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Food Log Ditambahkan',
            'message' => "Anda menambahkan {$request->food_name} dengan {$request->calories} kalori.",
            'is_read' => false,
        ]);

        return redirect()->route('food-log', ['date' => $date->format('Y-m-d')])
            ->with('success', 'Food log added successfully.');
    }

    public function update(Request $request, FoodLog $foodLog)
    {
        $this->authorize('update', $foodLog);

        $request->validate([
            'meal_type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
            'food_name' => 'required|string|max:255',
            'calories' => 'required|integer|min:0',
        ]);

        $foodLog->update([
            'meal_type' => $request->meal_type,
            'food_name' => $request->food_name,
            'calories' => $request->calories,
        ]);

        return redirect()->route('food-log', ['date' => $foodLog->date->format('Y-m-d')])
            ->with('success', 'Food log updated successfully.');
    }

    public function destroy(FoodLog $foodLog)
    {
        $this->authorize('delete', $foodLog);

        $date = $foodLog->date;
        $foodLog->delete();

        return redirect()->route('food-log', ['date' => $date->format('Y-m-d')])
            ->with('success', 'Food log deleted successfully.');
    }
}
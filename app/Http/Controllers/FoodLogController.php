<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\FoodLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FoodLogController extends Controller
{
    public function index()
    {
        $date = Carbon::today();
        $foodLogs = FoodLog::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->get()
            ->groupBy('meal_type');
        $caloriesToday = $foodLogs->sum('calories');
        $calorieGoal = 1800;

        return view('food-log', compact('foodLogs', 'caloriesToday', 'calorieGoal', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meal_type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
            'food_name' => 'required|string|max:255',
            'calories' => 'required|integer|min:0',
        ]);

        FoodLog::create([
            'user_id' => auth()->id(),
            'date' => Carbon::today(),
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

        return redirect()->route('food-log')->with('success', 'Food log added successfully.');
    }
}
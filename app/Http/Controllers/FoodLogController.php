<?php
namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\Notification;
use App\Services\FatSecretService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FoodLogController extends Controller
{
    protected $fatSecretService;

    public function __construct(FatSecretService $fatSecretService)
    {
        $this->middleware('auth');
        $this->fatSecretService = $fatSecretService;
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

        $searchQuery = $request->input('search_food');
        $foodResults = [];
        if ($searchQuery) {
            $foodResults = $this->fatSecretService->searchFood($searchQuery);
            \Log::info('FoodLogController: searchQuery=' . $searchQuery . ', foodResultsCount=' . count($foodResults) . ', foodResults=' . json_encode($foodResults));
        }

        \Log::info('FoodLog: date=' . $date->format('Y-m-d') . ', calories=' . $caloriesToday . ', goal=' . $calorieGoal . ', remaining=' . $remainingCalories);

        return view('food-log', compact('foodLogs', 'caloriesToday', 'calorieGoal', 'remainingCalories', 'date', 'foodResults', 'searchQuery'));
    }

    public function store(Request $request)
    {
        \Log::info('FoodLogController: store started', [
            'request_data' => $request->all(),
            'query_date' => $request->query('date'),
        ]);

        $date = Carbon::parse($request->query('date', Carbon::today()))->startOfDay();

        try {
            $request->validate([
                'meal_type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
                'food_id' => 'nullable|string',
                'food_name' => 'required_if:food_id,null|string|max:255',
                'calories' => 'required_if:food_id,null|integer|min:0',
                'carbohydrate' => 'nullable|numeric|min:0',
                'protein' => 'nullable|numeric|min:0',
                'fat' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('FoodLogController: Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $foodName = null;
        $calories = null;
        $carbohydrate = $request->has('carbohydrate') ? (float) $request->carbohydrate : null;
        $protein = $request->has('protein') ? (float) $request->protein : null;
        $fat = $request->has('fat') ? (float) $request->fat : null;

        if ($request->filled('food_id')) {
            $foodData = $this->fatSecretService->getFoodById($request->food_id);
            \Log::info('FoodLogController: food_id=' . $request->food_id . ', foodData=' . json_encode($foodData));

            if (empty($foodData) || empty($foodData['servings']['serving'])) {
                \Log::warning('FoodLogController: Invalid food data for food_id=' . $request->food_id);
                return redirect()->back()->withErrors(['food_id' => 'Makanan tidak ditemukan atau data tidak lengkap di FatSecret.']);
            }

            $servings = collect($foodData['servings']['serving']);
            $serving = $servings->firstWhere('is_default', '1') ?? $servings->first();
            if (empty($serving)) {
                \Log::warning('FoodLogController: No serving found for food_id=' . $request->food_id);
                return redirect()->back()->withErrors(['food_id' => 'Data porsi makanan tidak tersedia.']);
            }

            \Log::info('FoodLogController: Serving selected for food_id=' . $request->food_id, [
                'serving' => $serving,
            ]);

            $foodName = $foodData['food_name'];
            $calories = (int) $serving['calories'];
            $carbohydrate = isset($serving['carbohydrate']) ? (float) $serving['carbohydrate'] : null;
            $protein = isset($serving['protein']) ? (float) $serving['protein'] : null;
            $fat = isset($serving['fat']) ? (float) $serving['fat'] : null;
        } else {
            $foodName = $request->food_name;
            $calories = (int) $request->calories;
        }

        $foodLog = FoodLog::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'meal_type' => $request->meal_type,
            'food_name' => $foodName,
            'calories' => $calories,
            'carbohydrate' => $carbohydrate,
            'protein' => $protein,
            'fat' => $fat,
        ]);

        \Log::info('FoodLogController: Food log created', [
            'food_log_id' => $foodLog->id,
            'food_name' => $foodName,
            'calories' => $calories,
            'carbohydrate' => $carbohydrate,
            'protein' => $protein,
            'fat' => $fat,
            'meal_type' => $request->meal_type,
            'date' => $date->format('Y-m-d'),
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Food Log Ditambahkan',
            'message' => "Anda menambahkan {$foodName} dengan {$calories} kalori.",
            'is_read' => false,
        ]);

        return redirect()->route('food-log', ['date' => $date->format('Y-m-d')])
            ->with('success', 'Food log added successfully.');
    }

    public function update(Request $request, FoodLog $foodLog)
    {
        $this->authorize('update', $foodLog);

        try {
            $request->validate([
                'meal_type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
                'food_name' => 'required|string|max:255',
                'calories' => 'required|integer|min:0',
                'carbohydrate' => 'nullable|numeric|min:0',
                'protein' => 'nullable|numeric|min:0',
                'fat' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('FoodLogController: Validation failed on update', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $foodLog->update([
            'meal_type' => $request->meal_type,
            'food_name' => $request->food_name,
            'calories' => (int) $request->calories,
            'carbohydrate' => $request->has('carbohydrate') ? (float) $request->carbohydrate : null,
            'protein' => $request->has('protein') ? (float) $request->protein : null,
            'fat' => $request->has('fat') ? (float) $request->fat : null,
        ]);

        \Log::info('FoodLogController: Food log updated', [
            'food_log_id' => $foodLog->id,
            'food_name' => $request->food_name,
            'calories' => $request->calories,
            'meal_type' => $request->meal_type,
        ]);

        return redirect()->route('food-log', ['date' => $foodLog->date->format('Y-m-d')])
            ->with('success', 'Food log updated successfully.');
    }

    public function destroy(FoodLog $foodLog)
    {
        $this->authorize('delete', $foodLog);

        $date = $foodLog->date;
        $foodLog->delete();

        \Log::info('FoodLogController: Food log deleted', ['food_log_id' => $foodLog->id]);

        return redirect()->route('food-log', ['date' => $date->format('Y-m-d')])
            ->with('success', 'Food log deleted successfully.');
    }

    public function setCalorieGoal(Request $request)
    {
        $request->validate([
            'calorie_goal' => 'required|numeric|min:0',
        ]);

        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        $user = auth()->user();
        $user->calorie_goal = $request->calorie_goal;
        $user->save();

        return redirect()->route('food-log', ['date' => $date->format('Y-m-d')])->with('success', 'Target kalori berhasil diatur.');
    }
}
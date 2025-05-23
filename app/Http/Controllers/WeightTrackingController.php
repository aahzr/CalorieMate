<?php
namespace App\Http\Controllers;

use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WeightTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        $weightLogs = WeightLog::where('user_id', auth()->id())
            ->whereDate('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->formatted_date = $log->date->format('d M Y');
                return $log;
            });
        $currentWeight = $weightLogs->first();
        $targetWeight = auth()->user()->target_weight;
        $calorieGoalType = auth()->user()->calorie_goal_type ?? 'deficit'; // Default ke defisit jika belum diatur
        $progress = 0;

        if ($currentWeight && $targetWeight) {
            if ($calorieGoalType == 'deficit') {
                if ($currentWeight->weight > $targetWeight) {
                    $progress = round((($currentWeight->weight - $targetWeight) / ($currentWeight->weight)) * 100, 1);
                } else {
                    $progress = 100; // Sudah tercapai
                }
            } else {
                if ($currentWeight->weight < $targetWeight) {
                    $progress = round((($targetWeight - $currentWeight->weight) / ($targetWeight)) * 100, 1);
                } else {
                    $progress = 100; // Sudah tercapai
                }
            }
        }

        return view('weight-tracking', compact('weightLogs', 'currentWeight', 'targetWeight', 'calorieGoalType', 'progress', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0',
        ]);

        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        WeightLog::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'weight' => $request->weight,
        ]);

        return redirect()->route('weight-tracking', ['date' => $date->format('Y-m-d')])->with('success', 'Berat badan berhasil dicatat.');
    }

    public function setTarget(Request $request)
    {
        $request->validate([
            'target_weight' => 'required|numeric|min:0',
            'calorie_goal_type' => 'required|in:deficit,surplus',
        ]);

        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        $user = auth()->user();
        $user->target_weight = $request->target_weight;
        $user->calorie_goal_type = $request->calorie_goal_type;
        $user->save();

        return redirect()->route('weight-tracking', ['date' => $date->format('Y-m-d')])->with('success', 'Target berat badan berhasil diatur.');
    }

    public function destroy(Request $request, $id)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        $log = WeightLog::where('user_id', auth()->id())->findOrFail($id);
        $log->delete();

        return redirect()->route('weight-tracking', ['date' => $date->format('Y-m-d')])->with('success', 'Log berat berhasil dihapus.');
    }
}
<?php
namespace App\Http\Controllers;

use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Pastikan ini ada
use Illuminate\Support\Facades\Auth;

class WeightTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $date = Carbon::today();
        $weightLogs = WeightLog::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->get()
            ->map(function ($log) {
                $log->formatted_date = $log->date->format('d M Y'); // Format di controller
                return $log;
            });
        $currentWeight = $weightLogs->sortByDesc('date')->first();
        $targetWeight = 70;
        $progress = $currentWeight && $targetWeight ? round(($currentWeight->weight / $targetWeight) * 100, 1) : 0;
        $formattedWeek = $date->startOfWeek()->format('d M') . ' - ' . $date->endOfWeek()->format('d M Y');

        return view('weight-tracking', compact('weightLogs', 'currentWeight', 'targetWeight', 'progress', 'date', 'formattedWeek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0',
            'date' => 'nullable|date',
        ]);

        WeightLog::create([
            'user_id' => auth()->id(),
            'date' => $request->date ? Carbon::parse($request->date) : Carbon::today(),
            'weight' => $request->weight,
        ]);

        return redirect()->route('weight-tracking')->with('success', 'Berat badan berhasil dicatat.');
    }
}
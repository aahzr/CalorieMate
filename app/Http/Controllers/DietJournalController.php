<?php
// app/Http/Controllers/DietJournalController.php
namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DietJournalController extends Controller
{
    public function index()
    {
        $date = Carbon::today();
        $journals = Journal::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        return view('diet-journal', compact('journals', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'mood' => 'required|in:Happy,Neutral,Sad',
        ]);

        Journal::create([
            'user_id' => auth()->id(),
            'date' => Carbon::today(),
            'content' => $request->content,
            'mood' => $request->mood,
        ]);

        return redirect()->route('diet-journal')->with('success', 'Journal entry added successfully.');
    }
}
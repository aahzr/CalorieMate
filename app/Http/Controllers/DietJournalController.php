<?php
namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DietJournalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $journals = Journal::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('diet-journal', compact('journals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'mood' => 'required|in:Happy,Neutral,Sad',
        ]);

        Journal::create([
            'user_id' => auth()->id(),
            'date' => Carbon::now(),
            'content' => $request->content,
            'mood' => $request->mood,
        ]);

        return redirect()->route('diet-journal')->with('success', 'Journal entry added successfully.');
    }

    public function destroy($id)
    {
        $journal = Journal::where('user_id', auth()->id())->findOrFail($id);
        $journal->delete();

        return redirect()->route('diet-journal')->with('success', 'Journal entry deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = ReadingPlan::query()
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $readingPlans = $query->orderBy('target_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('reading-plans.index', compact('readingPlans'));
    }

    public function create()
    {

        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    public function store(StoreReadingPlanRequest $request)
    {
        $validated = $request->validated();

        Auth::user()->readingPlans()->create([
            'book_id' => $validated['book_id'],
            'target_date' => $validated['target_date'],
            'status' => '未着手',
            'target_pages' => 0,
        ]);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を作成しました。');
    }

    public function edit(ReadingPlan $readingPlan)
    {
        $this->authorize('view', $readingPlan);

        return view('reading-plans.edit', compact('readingPlan'));
    }

    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan)
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->update([
            'target_date' => $request->validated('target_date'),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画の期日を更新しました。');
    }

    public function complete(ReadingPlan $readingPlan)
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を「読了」にしました！お疲れ様でした！');
    }

    public function destroy(ReadingPlan $readingPlan)
    {
        $this->authorize('delete', $readingPlan);

        $readingPlan->delete();

        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました。');
    }
}

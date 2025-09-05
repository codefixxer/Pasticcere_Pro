<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends Controller
{
    /**
     * Whose incomes are visible in the table:
     * - Super/admin (created_by NULL): self + children
     * - Subaccount: self + parent
     */
    protected function visibleUserIds()
    {
        $u = Auth::user();

        if (is_null($u->created_by)) {
            $children = User::where('created_by', $u->id)->pluck('id');
            return collect([$u->id])->merge($children)->unique()->values();
        }

        return collect([$u->id, $u->created_by])->unique()->values();
    }

    /** Category IDs the current user is allowed to use: global + tenant owner */
    protected function allowedCategoryIdsForCurrentUser(): array
    {
        $u = Auth::user();
        $ownerId = IncomeCategory::ownerIdFor($u);

        return IncomeCategory::whereNull('user_id')
            ->orWhere('user_id', $ownerId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    /** Categories to show in the dropdown */
    protected function categoriesForCurrentUser()
    {
        $u = Auth::user();
        $ownerId = IncomeCategory::ownerIdFor($u);

        return IncomeCategory::whereNull('user_id')
            ->orWhere('user_id', $ownerId)
            ->orderByRaw('user_id IS NULL DESC')
            ->orderBy('name')
            ->get();
    }

    public function index()
    {
        $visible = $this->visibleUserIds();

        $incomes = Income::with(['user', 'category'])
            ->whereIn('user_id', $visible)
            ->orderBy('date', 'desc')
            ->paginate(15);

        $categories = $this->categoriesForCurrentUser();

        return view('frontend.incomes.index', compact('incomes', 'categories'));
    }

    public function show(Income $income)
    {
        $income->load('category');
        return view('frontend.incomes.show', compact('income'));
    }

    public function store(Request $request)
    {
        $allowedIds = $this->allowedCategoryIdsForCurrentUser();

        $data = $request->validate([
            'identifier'         => 'nullable|string|max:255',
            'amount'             => 'required|numeric|min:0',
            'date'               => 'required|date',
            'income_category_id' => 'nullable|integer|exists:income_categories,id',
        ]);

        $catId = isset($data['income_category_id']) ? (int) $data['income_category_id'] : null;
        if (!is_null($catId) && !in_array($catId, $allowedIds, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Categoria non valida.');
        }

        $data['user_id'] = Auth::id();
        Income::create($data);

        return redirect()->route('incomes.index')->with('success', 'Entrata registrata!');
    }

    public function edit(Income $income)
    {
        if (!$this->visibleUserIds()->contains($income->user_id)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $visible = $this->visibleUserIds();
        $incomes = Income::with(['user', 'category'])
            ->whereIn('user_id', $visible)
            ->orderBy('date', 'desc')
            ->paginate(15);

        $categories = $this->categoriesForCurrentUser();

        return view('frontend.incomes.index', compact('incomes', 'categories', 'income'));
    }

    public function update(Request $request, Income $income)
    {
        if (!$this->visibleUserIds()->contains($income->user_id)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $allowedIds = $this->allowedCategoryIdsForCurrentUser();

        $data = $request->validate([
            'identifier'         => 'nullable|string|max:255',
            'amount'             => 'required|numeric|min:0',
            'date'               => 'required|date',
            'income_category_id' => 'nullable|integer|exists:income_categories,id',
        ]);

        $catId = isset($data['income_category_id']) ? (int) $data['income_category_id'] : null;
        if (!is_null($catId) && !in_array($catId, $allowedIds, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Categoria non valida.');
        }

        $income->update($data);

        return redirect()->route('incomes.index')->with('success', 'Entrata aggiornata!');
    }

    public function destroy(Income $income)
    {
        if (!$this->visibleUserIds()->contains($income->user_id)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $income->delete();
        return back()->with('success', 'Entrata rimossa.');
    }
}

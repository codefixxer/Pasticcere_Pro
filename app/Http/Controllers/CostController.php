<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\User;
use App\Models\Income;
use App\Models\CostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CostController extends Controller
{
    /**
     * Show form to create a new cost.
     */
    public function create()
    {
        $user = Auth::user();

        // Visible users: me + my children (if I’m root) OR me + my creator (if I’m a child)
        if (is_null($user->created_by)) {
            $children = User::where('created_by', $user->id)->pluck('id');
            $visibleUserIds = $children->isEmpty()
                ? collect([$user->id])
                : $children->push($user->id);
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by]);
        }

        // Categories belonging to visible users OR global (user_id NULL)
        $categories = CostCategory::with('user')
            ->where(function ($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhereNull('user_id');
            })
            ->orderBy('name')
            ->get();

        return view('frontend.costs.create', compact('categories'));
    }

    /**
     * Display a single cost.
     */
    public function show(Cost $cost)
    {
        // Only owner can view (adapt if you want parent/child visibility for show)
        if ($cost->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('frontend.costs.show', compact('cost'));
    }

    /**
     * Persist a newly created cost.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier'        => ['required','string','max:255'],
            'cost_identifier' => ['nullable','string','max:255'],
            'amount'          => ['required','numeric','min:0'],
            'due_date'        => ['required','date'],
            'category_id'     => ['required','exists:cost_categories,id'],
            'other_category'  => ['nullable','string','max:255'],
        ]);

        // Ensure due_date is a Carbon date instance
        $data['due_date'] = $request->date('due_date');
        $data['user_id']  = Auth::id();

        Cost::create($data);

        return redirect()
            ->route('costs.index')
            ->with('success', 'Costo aggiunto!');
    }

    /**
     * Display a listing of costs.
     */
    public function index()
    {
        $user = Auth::user();

        if (is_null($user->created_by)) {
            $children = User::where('created_by', $user->id)->pluck('id');
            $visibleUserIds = $children->isEmpty()
                ? collect([$user->id])
                : $children->push($user->id);
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by]);
        }

        $categories = CostCategory::with('user')
            ->where(function ($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhereNull('user_id');
            })
            ->orderBy('name')
            ->get();

        $costs = Cost::with(['category','user'])
            ->whereIn('user_id', $visibleUserIds)
            ->orderBy('due_date', 'desc')
            ->get();

        return view('frontend.costs.index', compact('categories','costs'));
    }

    /**
     * Show the form for editing the specified cost.
     */
    public function edit(Cost $cost)
    {
        if ($cost->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $user = Auth::user();

        if (is_null($user->created_by)) {
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        $categories = CostCategory::with('user')
            ->where(function ($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhereNull('user_id');
            })
            ->orderBy('name')
            ->get();

        // Reuse the create view for edit
        return view('frontend.costs.create', compact('cost', 'categories'));
    }

    /**
     * Update the specified cost in storage.
     */
    public function update(Request $request, Cost $cost)
    {
        if ($cost->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'supplier'        => ['required','string','max:255'],
            'cost_identifier' => ['nullable','string','max:255'],
            'amount'          => ['required','numeric','min:0'],
            'due_date'        => ['required','date'],
            'category_id'     => ['required','exists:cost_categories,id'],
            'other_category'  => ['nullable','string','max:255'],
        ]);

        // Ensure due_date is a Carbon date instance
        $data['due_date'] = $request->date('due_date');

        $cost->update($data);

        return redirect()
            ->route('costs.index')
            ->with('success', 'Costo aggiornato con successo!');
    }

    /**
     * Remove the specified cost from storage.
     */
    public function destroy(Cost $cost)
    {
        if ($cost->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $cost->delete();

        return redirect()
            ->route('costs.index')
            ->with('success', 'Costo eliminato con successo!');
    }

    /**
     * Dashboard showing cost/income summaries.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (is_null($user->created_by)) {
            $children = User::where('created_by', $user->id)->pluck('id');
            $visibleUserIds = $children->isEmpty()
                ? collect([$user->id])
                : $children->push($user->id);
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by]);
        }

        $year     = (int) $request->query('y', now()->year);
        $month    = (int) $request->query('m', now()->month);
        $lastYear = $year - 1;

        // Categories for filters/legends
        $categories = CostCategory::with('user')
            ->where(function ($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhereNull('user_id');
            })
            ->orderBy('name')
            ->get();

        $availableYears = Cost::whereIn('user_id', $visibleUserIds)
            ->selectRaw('YEAR(due_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $raw = Cost::whereIn('user_id', $visibleUserIds)
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total','category_id');

        $costsThisYear = Cost::whereIn('user_id', $visibleUserIds)
            ->whereYear('due_date', $year)
            ->selectRaw('MONTH(due_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total','month');

        $costsLastYear = Cost::whereIn('user_id', $visibleUserIds)
            ->whereYear('due_date', $lastYear)
            ->selectRaw('MONTH(due_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total','month');

        $totalCostYear     = $costsThisYear->sum();
        $totalCostLastYear = $costsLastYear->sum();

        $incomeThisYearMonthly = [];
        $incomeLastYearMonthly = [];
        $netByMonth            = [];

        for ($m = 1; $m <= 12; $m++) {
            $i1 = Income::whereIn('user_id', $visibleUserIds)
                ->whereYear('date', $year)->whereMonth('date', $m)
                ->sum('amount');

            $i2 = Income::whereIn('user_id', $visibleUserIds)
                ->whereYear('date', $lastYear)->whereMonth('date', $m)
                ->sum('amount');

            $incomeThisYearMonthly[$m] = $i1;
            $incomeLastYearMonthly[$m] = $i2;
            $netByMonth[$m]            = $i1 - ($costsThisYear[$m] ?? 0);
        }

        $totalIncomeYear     = array_sum($incomeThisYearMonthly);
        $totalIncomeLastYear = array_sum($incomeLastYearMonthly);
        $netYear             = $totalIncomeYear - $totalCostYear;
        $netLastYear         = $totalIncomeLastYear - $totalCostLastYear;

        $bestNet   = max($netByMonth);
        $worstNet  = min($netByMonth);
        $bestMonth = array_search($bestNet, $netByMonth, true);
        $worstMonth = array_search($worstNet, $netByMonth, true);

        if (count(array_unique($netByMonth)) === 1) {
            $worstMonth = null;
            $worstNet   = $bestNet;
        }

        $incomeThisMonth    = $incomeThisYearMonthly[$month] ?? 0;
        $incomeLastYearSame = $incomeLastYearMonthly[$month] ?? 0;

        return view('frontend.costs.dashboard', compact(
            'availableYears','year','month','lastYear','categories',
            'raw','incomeThisMonth','incomeLastYearSame',
            'costsThisYear','costsLastYear','netByMonth',
            'incomeThisYearMonthly','incomeLastYearMonthly',
            'totalCostYear','totalIncomeYear','netYear',
            'totalCostLastYear','totalIncomeLastYear','netLastYear',
            'bestMonth','bestNet','worstMonth','worstNet'
        ));
    }
}

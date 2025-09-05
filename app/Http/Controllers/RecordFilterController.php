<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\ExternalSupply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordFilterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        //
        // 1) Build “visible” user IDs
        //
        if (is_null($user->created_by)) {
            // root → self + direct children
            $children = User::where('created_by', $user->id)->pluck('id');
            $visibleUserIds = $children->isEmpty()
                ? collect([$user->id])
                : $children->push($user->id);
        } else {
            // child → self + your creator
            $visibleUserIds = collect([$user->id, $user->created_by]);
        }

        //
        // 2) Date filters
        //
        $from = $request->query('from');
        $to   = $request->query('to');

        //
        // 3) Fetch & group Showcase
        //
        $showcases = Showcase::with('recipes.recipe.category','recipes.recipe.department')
            ->whereIn('user_id', $visibleUserIds)
            ->when($from, fn($q) => $q->whereDate('showcase_date','>=',$from))
            ->when($to,   fn($q) => $q->whereDate('showcase_date','<=',$to))
            ->orderBy('showcase_date')
            ->get();
        $showcaseGroups = $showcases->groupBy(fn($sc) => $sc->showcase_date->format('Y-m-d'));

        //
        // 4) Fetch & group ExternalSupply
        //
        $externals = ExternalSupply::with('client','recipes.recipe.category','recipes.recipe.department','recipes.returns')
            ->whereIn('user_id', $visibleUserIds)
            ->when($from, fn($q) => $q->whereDate('supply_date','>=',$from))
            ->when($to,   fn($q) => $q->whereDate('supply_date','<=',$to))
            ->orderBy('supply_date')
            ->get();
        $externalGroups = $externals->groupBy(fn($es) => $es->supply_date->format('Y-m-d'));

        //
        // 5) Grand totals (used in summaries and footers)
        //
        $totalShowcaseRevenue = $showcases
            ->flatMap(fn($sc) => $sc->recipes->pluck('actual_revenue'))
            ->sum();

        $totalExternalCost = $externals
            ->flatMap(fn($es) => $es->recipes->map(function($line) {
                $unit = $line->qty > 0
                      ? ($line->total_amount / $line->qty)
                      : 0;
                $returned = $line->returns->sum('qty') * $unit;
                return $line->total_amount - $returned;
            }))
            ->sum();

        return view('frontend.records.index', compact(
            'showcaseGroups',
            'externalGroups',
            'from','to',
            'totalShowcaseRevenue',
            'totalExternalCost'
        ));
    }
}

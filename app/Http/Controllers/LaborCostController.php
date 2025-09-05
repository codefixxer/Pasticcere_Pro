<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LaborCost;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaborCostController extends Controller
{
    private const SHARED_BUCKET_KEYS = [
        'electricity','leasing_loan','owner','van_rental','taxes','shop_assistants',
    ];

    private const BUCKET_KEYS = [
        'electricity','ingredients','leasing_loan','packaging','owner','van_rental','chefs',
        'shop_assistants','other_salaries','taxes','other_categories','driver_salary',
    ];

    public function index(Request $request)
    {
        $user         = Auth::user();
        $groupOwnerId = $user->created_by ?? $user->id;

        $editingId = $request->query('edit');

        if ($editingId) {
            $laborCost = LaborCost::where('user_id', $groupOwnerId)->findOrFail($editingId);
        } else {
            $laborCost = LaborCost::where('user_id', $groupOwnerId)
                ->whereNull('department_id')
                ->latest('updated_at')
                ->first();
        }

        $sharedCost = LaborCost::where('user_id', $groupOwnerId)
            ->whereNull('department_id')
            ->latest('updated_at')
            ->first();

        $visibleUserIds = is_null($user->created_by)
            ? User::where('created_by', $user->id)->pluck('id')->push($user->id)->unique()
            : collect([$user->id, $user->created_by])->unique();

        $departments = Department::whereIn('user_id', $visibleUserIds)
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();

        $allCosts = LaborCost::with('department')
            ->where('user_id', $groupOwnerId)
            ->orderByRaw('CASE WHEN department_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('department_id')
            ->latest('updated_at')
            ->get();

        $deptRows = LaborCost::where('user_id', $groupOwnerId)
            ->whereNotNull('department_id')
            ->get();

        $sharedMonthly = (float) optional($sharedCost)->monthly_bep ?: 0.0;
        $sharedDaily   = (float) optional($sharedCost)->daily_bep   ?: 0.0;

        $deptMonthlySum = (float) $deptRows->sum('monthly_bep');
        $deptDailySum   = (float) $deptRows->sum('daily_bep');

        $globalMonthlyBEP = $sharedMonthly + $deptMonthlySum;
        $globalDailyBEP   = $sharedDaily   + $deptDailySum;

        return view('frontend.labor-cost.index', compact(
            'laborCost',
            'sharedCost',
            'departments',
            'allCosts',
            'editingId',
            'globalMonthlyBEP',
            'globalDailyBEP'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'num_chefs'     => 'required|numeric|min:0',
            'opening_days'  => 'required|integer|min:1',
            'hours_per_day' => 'required|numeric|min:0',

            'electricity'      => 'nullable|numeric|min:0',
            'ingredients'      => 'nullable|numeric|min:0',
            'leasing_loan'     => 'nullable|numeric|min:0',
            'packaging'        => 'nullable|numeric|min:0',
            'owner'            => 'nullable|numeric|min:0',
            'van_rental'       => 'nullable|numeric|min:0',
            'chefs'            => 'nullable|numeric|min:0',
            'shop_assistants'  => 'nullable|numeric|min:0',
            'other_salaries'   => 'nullable|numeric|min:0',
            'taxes'            => 'nullable|numeric|min:0',
            'other_categories' => 'nullable|numeric|min:0',
            'driver_salary'    => 'nullable|numeric|min:0',

            'monthly_bep'           => 'nullable|numeric',
            'daily_bep'             => 'nullable|numeric',
            'shop_cost_per_min'     => 'nullable|numeric',
            'external_cost_per_min' => 'nullable|numeric',

            'department_id' => 'nullable|exists:departments,id',
            'incidence_pct' => 'nullable|numeric|min:0|max:100',

            'editing_id'    => 'nullable|exists:labor_costs,id',
        ]);

        foreach (self::BUCKET_KEYS as $k) {
            $data[$k] = $data[$k] ?? 0;
        }

        $user         = Auth::user();
        $groupOwnerId = $user->created_by ?? $user->id;

        if (!empty($data['department_id']) && $request->filled('incidence_pct')) {
            Department::where('id', $data['department_id'])
                ->update(['share_percent' => $data['incidence_pct']]);
        }

        $isDepartmentRow = !empty($data['department_id']);
        if ($isDepartmentRow) {
            foreach (self::SHARED_BUCKET_KEYS as $k) {
                $data[$k] = 0;
            }
        }

        $data = $this->recomputeFinancials($data, $groupOwnerId);

        if ($request->filled('editing_id')) {
            $row = LaborCost::where('user_id', $groupOwnerId)->findOrFail($request->editing_id);
            $row->fill(array_merge($data, [
                'user_id'       => $groupOwnerId,
                'department_id' => $data['department_id'] ?? null,
            ]));
            $row->save();
        } else {
            $match = [
                'user_id'       => $groupOwnerId,
                'department_id' => $data['department_id'] ?? null,
            ];
            unset($data['incidence_pct']);
            $row = LaborCost::updateOrCreate($match, array_merge($data, [
                'user_id' => $groupOwnerId,
            ]));
        }

        return redirect()
            ->route('labor-cost.index', ['edit' => $row->id])
            ->with('success', 'Dettagli Costo Lavoro e BEP salvati.');
    }

    public function destroy(LaborCost $laborCost)
    {
        $user         = Auth::user();
        $groupOwnerId = $user->created_by ?? $user->id;
        abort_unless($laborCost->user_id == $groupOwnerId, 403);

        if ($laborCost->department_id) {
            Department::where('id', $laborCost->department_id)->update(['share_percent' => null]);
        }

        $laborCost->delete();

        return back()->with('success', 'Record eliminato.');
    }

    public function show(Request $request, $id = null)
    {
        $user         = Auth::user();
        $groupOwnerId = $user->created_by ?? $user->id;
        $deptId       = $request->query('department_id');

        $global = LaborCost::where('user_id', $groupOwnerId)
            ->whereNull('department_id')
            ->latest('updated_at')
            ->first();

        $override = null;
        $incidence = null;

        if ($deptId) {
            $override = LaborCost::where('user_id', $groupOwnerId)
                ->where('department_id', $deptId)
                ->latest('updated_at')
                ->first();

            $incidence = $override ? optional(Department::find($deptId))->share_percent : null;
        }

        $row = [
            'id'            => $override?->id ?? ($deptId ? null : $global?->id),
            'department_id' => $deptId ?: null,
            'incidence_pct' => $incidence,
            'num_chefs'     => $override?->num_chefs     ?? $global?->num_chefs     ?? 1,
            'opening_days'  => $override?->opening_days  ?? $global?->opening_days  ?? 22,
            'hours_per_day' => $override?->hours_per_day ?? $global?->hours_per_day ?? 8,
        ];

        foreach (self::BUCKET_KEYS as $k) {
            if ($deptId) {
                if ($override) {
                    $row[$k] = in_array($k, self::SHARED_BUCKET_KEYS, true) ? 0 : ($override->{$k} ?? 0);
                } else {
                    $row[$k] = $global?->{$k} ?? 0;
                }
            } else {
                $row[$k] = $global?->{$k} ?? 0;
            }
        }

        return response()->json($row);
    }

    private function recomputeFinancials(array $data, int $groupOwnerId): array
    {
        $monthlyRow = 0.0;
        foreach (self::BUCKET_KEYS as $k) {
            $monthlyRow += (float) ($data[$k] ?? 0);
        }

        $days  = max(1, (int)($data['opening_days'] ?? 1));
        $mins  = $days * (float)($data['hours_per_day'] ?? 0) * 60.0;
        $chefs = max(0.1, (float)($data['num_chefs'] ?? 0.1));

        $ing = (float)($data['ingredients']   ?? 0);
        $van = (float)($data['van_rental']    ?? 0);
        $drv = (float)($data['driver_salary'] ?? 0);
        $sa  = (float)($data['shop_assistants'] ?? 0);

        $monthlyForRates = $monthlyRow;
        $saShare = $sa;

        if (!empty($data['department_id'])) {
            $deptId = (int)$data['department_id'];
            $sharePct = isset($data['incidence_pct']) && $data['incidence_pct'] !== null
                ? (float)$data['incidence_pct']
                : (float) optional(Department::find($deptId))->share_percent;

            if ($sharePct === 0.0 || $sharePct === null) {
                $sharePct = 100.0;
            }

            $global = LaborCost::where('user_id', $groupOwnerId)
                ->whereNull('department_id')
                ->latest('updated_at')
                ->first();

            $sharedMonthly = 0.0;
            $sharedAssistants = 0.0;

            if ($global) {
                foreach (self::SHARED_BUCKET_KEYS as $k) {
                    $sharedMonthly += (float) $global->$k;
                }
                $sharedAssistants = (float) $global->shop_assistants;
            }

            $add = $sharedMonthly * ($sharePct / 100.0);
            $monthlyForRates += $add;
            $saShare = $sharedAssistants * ($sharePct / 100.0);
        }

        $shopOfficial     = $mins > 0 ? ($monthlyForRates - $ing - $van - $drv) / $mins / $chefs : 0.0;
        $externalOfficial = $mins > 0 ? ($monthlyForRates - $ing - $saShare)   / $mins / $chefs : 0.0;

        $data['monthly_bep']           = round($monthlyRow, 2);
        $data['daily_bep']             = round($monthlyRow / $days, 2);
        $data['shop_cost_per_min']     = round($shopOfficial / 3 * 4, 4);
        $data['external_cost_per_min'] = round($externalOfficial / 3 * 4, 4);

        return $data;
    }

    public function ajaxFetch(Request $request)
    {
        return $this->show($request, null);
    }
}

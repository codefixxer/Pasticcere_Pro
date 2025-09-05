<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use App\Models\LaborCost;

class LaborCostCalculator
{
    public static function effectiveRates(?User $user, ?Department $dept): array
    {
        if (!$user) {
            return ['shop' => 0.0, 'external' => 0.0];
        }

        $groupOwnerId = $user->created_by ?? $user->id;

        $global = LaborCost::where('user_id', $groupOwnerId)
            ->whereNull('department_id')
            ->latest('updated_at')
            ->first();

        if (!$global) {
            return ['shop' => 0.0, 'external' => 0.0];
        }

        $override = null;
        if ($dept) {
            $override = LaborCost::where('user_id', $groupOwnerId)
                ->where('department_id', $dept->id)
                ->latest('updated_at')
                ->first();
        }

        $share = $dept && $dept->share_percent !== null
            ? max(0, (float)$dept->share_percent) / 100.0
            : 1.0;

        $opening_days = self::coalesce($override, $global, 'opening_days', 22);
        $hours_per_day = self::coalesce($override, $global, 'hours_per_day', 8);
        $num_chefs = max(0.1, (float) self::coalesce($override, $global, 'num_chefs', 1));
        $mins = max(1, (int)$opening_days) * max(0, (float)$hours_per_day) * 60;

        $sharedKeys = [
            'electricity',
            'leasing_loan',
            'owner',
            'van_rental',
            'taxes',
            'shop_assistants',
        ];

        $sharedTotal = 0.0;
        foreach ($sharedKeys as $k) {
            $globalVal = (float) ($global->{$k} ?? 0);
            $sharedTotal += $globalVal * $share;
        }

        $perDept = [
            'ingredients'      => (float) ($override?->ingredients      ?? 0),
            'packaging'        => (float) ($override?->packaging        ?? 0),
            'other_categories' => (float) ($override?->other_categories ?? 0),
            'chefs'            => (float) ($override?->chefs            ?? 0),
            'other_salaries'   => (float) ($override?->other_salaries   ?? 0),
            'driver_salary'    => (float) ($override?->driver_salary    ?? 0),
            'van_rental'       => (float) ($override?->van_rental       ?? 0),
        ];

        $total = $sharedTotal
               + $perDept['ingredients']
               + $perDept['packaging']
               + $perDept['other_categories']
               + $perDept['chefs']
               + $perDept['other_salaries']
               + $perDept['driver_salary']
               + $perDept['van_rental'];

        $shopBase = $mins > 0
            ? ($total - $perDept['ingredients'] - $perDept['van_rental'] - $perDept['driver_salary']) / $mins / $num_chefs
            : 0.0;

        $externalBase = $mins > 0
            ? ($total - $perDept['ingredients'] - ((float)($global->shop_assistants ?? 0) * $share)) / $mins / $num_chefs
            : 0.0;

        $shop = round(($shopBase / 3) * 4, 4);
        $external = round(($externalBase / 3) * 4, 4);

        return ['shop' => max(0, $shop), 'external' => max(0, $external)];
    }

    private static function coalesce(?LaborCost $override, LaborCost $global, string $key, $default)
    {
        $ov = $override ? $override->{$key} : null;
        return ($ov !== null) ? $ov : ($global->{$key} ?? $default);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;

class LaborCost extends Model
{
    /**
     * Mass assignable fields.
     * Note: `is_default` is optional/backward-compatible and
     *       is NOT required to mark a row as "global".
     */
    protected $fillable = [
        'name',                 // optional label
        'is_default',           // optional legacy flag; NOT required
        'department_id',        // NULL => global/shared; set => dept override

        // capacity
        'num_chefs',
        'opening_days',
        'hours_per_day',

        // cost buckets
        'electricity',
        'ingredients',
        'leasing_loan',
        'packaging',
        'owner',
        'van_rental',
        'chefs',
        'shop_assistants',
        'other_salaries',
        'taxes',
        'other_categories',
        'driver_salary',

        // computed (optional)
        'monthly_bep',
        'daily_bep',
        'shop_cost_per_min',
        'external_cost_per_min',

        'user_id',
    ];

    protected $casts = [
        'is_default'            => 'boolean',
        'num_chefs'             => 'float',
        'opening_days'          => 'integer',
        'hours_per_day'         => 'float',
        'electricity'           => 'float',
        'ingredients'           => 'float',
        'leasing_loan'          => 'float',
        'packaging'             => 'float',
        'owner'                 => 'float',
        'van_rental'            => 'float',
        'chefs'                 => 'float',
        'shop_assistants'       => 'float',
        'other_salaries'        => 'float',
        'taxes'                 => 'float',
        'other_categories'      => 'float',
        'driver_salary'         => 'float',
        'monthly_bep'           => 'float',
        'daily_bep'             => 'float',
        'shop_cost_per_min'     => 'float',
        'external_cost_per_min' => 'float',
    ];

    /* -----------------------------
     | Relationships
     * ---------------------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /* -----------------------------
     | Query Scopes
     * ---------------------------- */

    /**
     * Scope: rows owned by the given (group owner) user_id.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: "global/shared" row(s) — we ONLY check department_id IS NULL.
     * This avoids failures when is_default was never set.
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('department_id');
    }

    /**
     * Scope: department override row(s) for a given department.
     */
    public function scopeForDepartment($query, $deptId)
    {
        return $query->where('department_id', $deptId);
    }
}

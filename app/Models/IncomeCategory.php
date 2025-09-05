<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    /**
     * Owner id in a tenant sense:
     * - super/admin (created_by = null): their own id
     * - subaccount (created_by != null): parent admin id
     */
    public static function ownerIdFor(User $user): int
    {
        return is_null($user->created_by) ? $user->id : (int) $user->created_by;
    }

    /**
     * Categories visible to a user:
     * - Global (user_id NULL) for everyone
     * - Plus the tenant owner id (admin or that admin for subaccounts)
     * - Super sees global + their own
     */
    public function scopeVisibleTo($q, User $user)
    {
        $ownerId = self::ownerIdFor($user);
        return $q->whereNull('user_id')
                 ->orWhere('user_id', $ownerId);
    }
}

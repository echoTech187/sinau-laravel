<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'invoice_number', 'amount', 'description'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The "booted" method of the model.
     * This is where Row-Level Security (Data Scoping) happens.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('branch_access', function (Builder $builder) {
            $user = auth()->user();
            
            // Apply restrictions only if user is logged in
            if ($user) {
                // Returns null if super-admin, or array if restricted, or empty array if totally blocked
                $branchScope = $user->getDataScope('branch_id');
                
                // If it's an array, it means there is a restriction
                if (is_array($branchScope)) {
                    // Empty array means the user has a secondary role with branch_id scope, 
                    // but the array is empty meaning they shouldn't see any branch data unless defined.
                    // To handle cases naturally, we just restrict to the given IDs
                    $builder->whereIn('branch_id', $branchScope);
                }
                // If $branchScope is exactly null, we do not apply whereIn() filter,
                // letting the query fetch all data (unrestricted).
            }
        });
    }
}

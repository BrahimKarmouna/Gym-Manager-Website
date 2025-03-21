<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Gym;
use App\Models\User;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id', 
        'description', 
        'amount', 
        'bill_date', 
        'user_id',
        'status',
        'due_date',
        'category',
        'type'
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'amount' => 'float',
        'status' => 'string',
        'type' => 'string'
    ];
    
    // Expense categories with associated icons
    const CATEGORIES = [
        'rent' => [
            'label' => 'Rent',
            'icon' => 'home'
        ],
        'utilities' => [
            'label' => 'Utilities',
            'icon' => 'bolt'
        ],
        'water' => [
            'label' => 'Water Bill',
            'icon' => 'water_drop'
        ],
        'equipment' => [
            'label' => 'Equipment',
            'icon' => 'fitness_center'
        ],
        'maintenance' => [
            'label' => 'Maintenance',
            'icon' => 'build'
        ],
        'salary' => [
            'label' => 'Salary',
            'icon' => 'payments'
        ],
        'insurance' => [
            'label' => 'Insurance',
            'icon' => 'shield'
        ],
        'marketing' => [
            'label' => 'Marketing',
            'icon' => 'campaign'
        ],
        'taxes' => [
            'label' => 'Taxes',
            'icon' => 'receipt_long'
        ],
        'supplies' => [
            'label' => 'Supplies',
            'icon' => 'inventory_2'
        ],
        'software' => [
            'label' => 'Software',
            'icon' => 'computer'
        ],
        'renovation' => [
            'label' => 'Renovation',
            'icon' => 'construction'
        ],
        'decoration' => [
            'label' => 'Decoration',
            'icon' => 'palette'
        ],
        'furniture' => [
            'label' => 'Furniture',
            'icon' => 'chair'
        ],
        'other' => [
            'label' => 'Other',
            'icon' => 'more_horiz'
        ]
    ];

    /**
     * Get the formatted amount with currency symbol
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Check if the expense is overdue
     * 
     * @return bool
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'paid' || $this->type === 'enhancement') {
            return false;
        }
        
        return $this->due_date && Carbon::parse($this->due_date)->isPast();
    }

    /**
     * Scope a query to only include bills
     */
    public function scopeBills($query)
    {
        return $query->where('type', 'bill');
    }

    /**
     * Scope a query to only include enhancements
     */
    public function scopeEnhancements($query)
    {
        return $query->where('type', 'enhancement');
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bill extends Model
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
        'category'
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'amount' => 'float',
    ];
    
    // Bill categories with associated icons
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
     * Check if the bill is overdue
     * 
     * @return bool
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'paid') {
            return false;
        }
        
        return $this->due_date && Carbon::parse($this->due_date)->isPast();
    }

    /**
     * Auto-update status to overdue if needed before saving
     */
    protected static function booted()
    {
        static::saving(function ($bill) {
            // If the bill is pending and due date is in the past, mark as overdue
            if ($bill->status === 'pending' && $bill->due_date && Carbon::parse($bill->due_date)->isPast()) {
                $bill->status = 'overdue';
            }
        });
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

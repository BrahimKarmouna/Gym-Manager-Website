<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

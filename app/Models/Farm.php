<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_name',
        'province_name',
        'city_municipality_name',
        'barangay_name',
        'watermelon_variety',
        'planting_date',
        'field_size',
        'field_size_unit',
    ];

    protected $casts = [
        'planting_date' => 'date',
        'field_size' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class Records extends Model
{
    use HasFactory;

    protected $fillable = [
        'indication_id',
        'station_id',
        'proportion',
        'unit',
        'measurement_at'
    ];
    protected $table = 'records';
}

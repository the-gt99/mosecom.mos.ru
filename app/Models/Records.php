<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Records extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'indication_type_id',
        'proportion',
        'proportion_time',
        'unit',
        'unit_time'
    ];
    protected $table = 'records';
}

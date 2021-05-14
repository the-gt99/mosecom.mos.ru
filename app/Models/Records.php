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

    protected $table = 'records';

    protected $fillable = [
        'indication_id',
        'station_id',
        'error_id',
        'proportion',
        'unit',
        'measurement_at'
    ];

    public function indication()
    {
        return $this->hasOne(TypeOfIndication::class, 'id', 'indication_id');
    }

    public function station()
    {
        return $this->hasOne(Stations::class, 'station_id', 'id');
    }

    public function error()
    {
        return $this->hasOne(Errors::class, 'error_id', 'id');
    }
}

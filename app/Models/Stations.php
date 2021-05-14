<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class Stations extends Model
{
    use HasFactory;

    protected $table = 'stations';

    protected $fillable = [
        'name',
        'address',
        'lat',
        'lon',
        'type_primaty_key',
        'type',
        'wind_direction'
    ];

    public $timestamps = false;

    public function records()
    {
        return $this->hasMany(Records::class, 'station_id', 'id');
    }
}

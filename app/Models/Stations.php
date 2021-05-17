<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class Stations extends Model
{
    use HasFactory, SpatialTrait;

    protected $table = 'stations';

    protected $fillable = [
        'name',
        'address',
        'type_primaty_key',
        'type',
        'wind_direction'
    ];

    protected $spatialFields = [
        'point',
    ];

    public $timestamps = false;

    public function records()
    {
        return $this->hasMany(Records::class, 'station_id', 'id');
    }
}

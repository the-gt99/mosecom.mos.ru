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

    protected $fillable = [
        'name',
        'url'
    ];
    protected $table = 'stations';
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */

class Errors extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'measurement_at',
        'record_id'
    ];
    protected $table = 'errors';
}

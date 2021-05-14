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

    protected $table = 'errors';

    protected $fillable = [
        'message',
    ];

    public function record()
    {
        return $this->hasOne(Records::class, 'record_id', 'id');
    }
}

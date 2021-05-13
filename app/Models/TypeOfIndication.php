<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class TypeOfIndication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
    protected $table = 'type_of_indication';
    public $timestamps = false;
}

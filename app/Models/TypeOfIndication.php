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

    protected $table = 'type_of_indication';

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function records()
    {
        return $this->hasMany(Records::class, 'indication_id', 'id');
    }
}

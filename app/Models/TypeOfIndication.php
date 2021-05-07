<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfIndication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
    protected $table = 'type_of_indication';
}

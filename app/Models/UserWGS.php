<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWGS extends Model
{
    use HasFactory;
    protected $table = 'user';

    protected $casts = [
        'id' => 'string'
    ];
}

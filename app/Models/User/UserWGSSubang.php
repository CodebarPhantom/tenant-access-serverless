<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWGSSubang extends Model
{
    use HasFactory;
    protected $table = 'user';
    protected $casts = [
        'id' => 'string'
    ];
}

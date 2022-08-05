<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWGSKarawang extends Model
{
    use HasFactory;
    protected $connection = 'mysql-karawang';
    protected $table = 'user';

    protected $casts = [
        'id' => 'string'
    ];
}

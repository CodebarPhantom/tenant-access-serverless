<?php

namespace App\Models\TenantAccess;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;


class TenantAccessSlaCalculate extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
    protected $connection = 'mysql-karawang';

}

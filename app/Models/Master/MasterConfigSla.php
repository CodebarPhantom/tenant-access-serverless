<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterConfigSla extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'mysql-karawang';
    protected $table = 'master_config_sla';
    protected $casts = [
        'id' => 'string'
    ];
}

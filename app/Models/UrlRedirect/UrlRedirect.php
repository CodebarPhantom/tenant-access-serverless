<?php

namespace App\Models\UrlRedirect;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class UrlRedirect extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;
}

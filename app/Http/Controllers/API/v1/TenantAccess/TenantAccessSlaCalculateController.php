<?php

namespace App\Http\Controllers\API\v1\TenantAccess;

use Illuminate\Http\Request;
use App\Http\Controllers\Main\ApiControllerV1;
use DB;



class TenantAccessSlaCalculateController extends ApiControllerV1
{
    public function index()
    {
        $func = function () {
            $sesuatu = DB::connection('mysql-karawang')->table('view_tenant_access_report_dashboard')->paginate(10);

            $this->data = compact("sesuatu");
        };

        return $this->callFunction($func);

    }
}

<?php

namespace App\Http\Controllers\API\v1\UrlRedirect;

use Illuminate\Http\Request;
use App\Http\Controllers\Main\ApiControllerV1;
use App\Models\UrlRedirect\UrlRedirect;

class UrlRedirectController extends ApiControllerV1
{
    public function index()
    {
        $func = function () {

            $response = "What are you looking for?";

            $this->data = compact("response");
        };

        return $this->callFunction($func);
    }

    public function create()
    {
        $func = function () {
        };

        return $this->callFunction($func);
    }
}

<?php

namespace App\Http\Controllers\API\v1\UrlRedirect;

use Illuminate\Http\Request;
use App\Http\Controllers\Main\ApiControllerV1;
use App\Models\UrlRedirect\UrlRedirect;
use Illuminate\Support\Str;

class UrlRedirectController extends ApiControllerV1
{
    public function index()
    {
        $func = function () {

            $url_redirects = UrlRedirect::paginate(10);

            $this->data = compact("url_redirects");
        };

        return $this->callFunction($func);
    }

    public function store(Request $request)
    {
        $func = function () use ($request) {

            $this->validate($request, [
                "name" => ["required"],
                "to_url" => ["required"],
                "app_information" => ["required"]
            ]);

            $urlRedirect = new UrlRedirect();
            $urlRedirect->name = $request->name;
            $urlRedirect->slug = Str::slug($urlRedirect->name);
            $urlRedirect->to_url = $request->to_url;
            $urlRedirect->app_information = $request->app_information;
            $urlRedirect->save();

            array_push($this->messages, $urlRedirect->name . " URL Berhasil Disimpan");

        };

        return $this->callFunction($func);
    }
}

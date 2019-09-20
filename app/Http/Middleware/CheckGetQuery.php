<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Middleware\Illumina;

class CheckGetQuery
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if ($request->has("q")) {
            $queryString = Illumina::IlluminaCipherDecrypt($request["q"]);
            $queryCollections = [];
            parse_str($queryString, $queryCollections);
            foreach ($queryCollections as $key => $value) {
                $request[$key] = $value;
            }
            $request["q"] = null;
        }
        return $next($request);
    }
}

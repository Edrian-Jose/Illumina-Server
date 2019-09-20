<?php

namespace App\Http\Middleware;

use Closure;
use InvalidArgumentException;
use App\Http\Middleware\Illumina;

class CheckGetTimeUrl
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
        $requestKey =  $request->key;
        $keys = [];
        for ($i = 0; $i < 5; $i++) {
            $keys[$i] = Illumina::CreateUniqueDateTimeKey($i);
        }
        for ($j = 0; $j < 5; $j++) {
            if (Illumina::CompareIlluminaHashes($keys[$j], $requestKey)) {
                return $next($request);
            }
        }
        return redirect('/')->withException(InvalidArgumentException);
        // if ($requestKey == $key1 || $requestKey == $key2) {
        //     return $next($request);
        // } else {
        //     return redirect('/')->withException(InvalidArgumentException);
        // }
        //
    }
}

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



    public static function CheckUniqueKey($request)
    {
        $requestKey =  $request->key;
        $keys = [];
        for ($i = 0; $i < 3; $i++) {
            $keys[$i] = Illumina::CreateUniqueDateTimeKey($i);
        }
        for ($j = 0; $j < 3; $j++) {
            if (Illumina::CompareIlluminaHashes($keys[$j], $requestKey)) {
                return true;
            }
        }
        return false;
    }

    public function handle($request, Closure $next)
    {
        if (CheckGetTimeUrl::CheckUniqueKey($request)) {
            return $next($request);
        }
        return redirect('/')->withException(InvalidArgumentException);
    }
}

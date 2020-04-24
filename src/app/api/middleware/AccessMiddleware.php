<?php


namespace app\api\middleware;

use think\Response;
use think\Request;

class AccessMiddleware
{
    /**
     * 
     */
    public function handle($request, \Closure $next)
    {
        if($request->isOptions()){
            $response = Response::create();
        }else{
            $response = $next($request);
        }
        
        static::allowAcrossDomain($response);
        
        return $response;
    }

    public static $acrossHeaders=[
        'Access-Control-Allow-Origin'=>'*',
        'Access-Control-Allow-Methods'=>'*',
        'Access-Control-Allow-Headers'=>'*'
    ];

    /**
     * @param $response Response
     * @return Response
     */
    public static function allowAcrossDomain($response){
        $response->header(static::$acrossHeaders);
        return $response;
    }
}


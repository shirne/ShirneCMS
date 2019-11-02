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
        
        $response->header('Access-Control-Allow-Origin','*');
        $response->header('Access-Control-Allow-Methods','*');
        $response->header('Access-Control-Allow-Headers','x-requested-with,content-type,token');
        
        return $response;
    }
}


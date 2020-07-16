<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;

class ApiAuthMiddleware
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
        //comprobar si el usuario esta autenticado

       $token= $request->header('Authorization');

       $jwtAuth= new JwtAuth();

       $checkToken= $jwtAuth->checkToken($token);

       if($checkToken ){
        return $next($request);
    }else{
        $data= array(

            'code'=>400,
            'status'=>'error',
            'message'=>'El usuario no esta autenticado'
            );
            return response()->json($data, $data['code']);
    }
    }
}

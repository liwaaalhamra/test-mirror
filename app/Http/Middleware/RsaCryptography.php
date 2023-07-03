<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\CryptographyTrait;

class RsaCryptography
{
    use ResponseTrait;
    use CryptographyTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try
        {
            $validator = Validator::make( $request->all(), [
                'data' => 'required|string'
            ]);
    
            if( $validator->fails() ) throw new \Exception('Invalid request.');

            $decrypted_data = $this->rsa( $request->data, 'decrypt' );

        }
        catch( \Exception $e)
        {
            return $this->api_error_response($e->getMessage(), 111, $e->getMessage());
        }

        $request->merge( $decrypted_data['data'] );

        $request->request->remove('data');

        return $next($request);
    }
}

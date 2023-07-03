<?php

namespace App\Http\Traits;

use phpseclib3\Crypt\RSA;
use Illuminate\Support\Facades\Log;

trait CryptographyTrait
{
    function aes($iv, $action, $string, $with_hashing = false, $with_base64 = true)
    {
        $encrypt_method = config('services.cryptography.method');
        $secret_key = config('services.cryptography.secret_key');
        $secret_iv = $iv;

        /**
         * without hashing
         * iv - encrypt method AES-256-CBC expects 16 bytes
         */
        $key = $secret_key;
        $iv = substr( $secret_iv, 0, 16);

        /**
         * with hashing
         * iv - encrypt method AES-256-CBC expects 16 bytes
         */
        if( $with_hashing )
        {
            $key = hash( 'sha256', $secret_key );
            $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        }

        if( $action == 'encrypt' )
        {
            if( $with_base64 ) return base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'decrypt' )
        {
            if( $with_base64 ) return openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return false;
    }

    function rsa( $data, $action )
    {
        $privateKeyData = file_get_contents( base_path('private.pem') );

        try
        {
            $decrypted_data = Rsa::load($privateKeyData)
                ->withPadding( RSA::ENCRYPTION_PKCS1 )
                ->decrypt( base64_decode( $data ) );

            $decrypted_data = json_decode( $decrypted_data, true );

            if( is_null($decrypted_data) )
            {
                Log::info( $decrypted_data );
                throw new \Exception('Incorrect encrypted data');
            }
        }
        catch( \Exception $e )
        {
            Log::error( $e->getMessage() );
            throw new \Exception('Error occurred.');
        }

        return [
            'data' => $decrypted_data
        ];
    }
}

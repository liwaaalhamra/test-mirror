<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JoshuaChinemezu\SmsGlobal\RestApi\RestApiClient;
use App\Services\SmsGlobalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class SmsController extends Controller
{
    public function sendOTP()
    {
        $otp = $this->generateNumericOTP();
        $apiUrl = 'https://api.smsglobal.com/v2/sms';
        $phoneNumber = '96178837976';
        $message = 'Please verify using the following code: '.$otp;
        $senderId = 'Barterdom';

        $apiKey = env('SMSGLOBAL_API_KEY');
        $secretKey = env('SMSGLOBAL_SECRET_KEY');

        $ts = time();
        $nonce = Str::random(8);
        $url = 'https://api.smsglobal.com/v2/sms';
        $urlComponents = parse_url($url);

        $auth = "{$ts}\n{$nonce}\nPOST\n{$urlComponents['path']}\n{$urlComponents['host']}\n443\n\n";
        $authHash = base64_encode(hash_hmac('sha256', $auth, $secretKey, true));
        $authHeader = "MAC id=\"{$apiKey}\", ts=\"{$ts}\", nonce=\"{$nonce}\", mac=\"{$authHash}\"";

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => $authHeader,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'destination' => $phoneNumber,
                    'message' => $message,
                    'origin' => $senderId,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                // SMS sent successfully
                return response()->json(['message' => 'SMS sent successfully']);
            } else {
                // Handle error if needed
                return response()->json(['error' => 'Failed to send SMS']);
            }
        } catch (\Exception $e) {
            // Handle exceptions, log errors, etc.
            return response()->json(['error' => 'Error sending SMS: ' . $e->getMessage()]);
        }
    }

    function generateNumericOTP($length = 6)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return strval(random_int($min, $max));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

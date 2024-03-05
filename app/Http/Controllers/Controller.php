<?php

namespace App\Http\Controllers;

use App\Models\Api\Category;
use App\Models\Api\Country;
use App\Models\Api\CountryLanguages;
use App\Models\Api\Notification;
use App\Models\Api\UserPdf;
use App\Models\Api\Users;
use App\Models\Api\UsersAccess;
use Carbon\Carbon;
use Config;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    var $lang = 'en';
    var $sections = [];
    public function __construct()
    {
        if (strpos(\Route::current()->getPrefix(), 'admin') !== false) {
            foreach (auth()->user()->access_sections as $section){
                $this->sections[] = $section->section;
            }
        }
    }

    function buildBaseString($baseURI, $method, $params){
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }

        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    function buildAuthorizationHeader($oauth){
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";

        $r .= implode(', ', $values);
        return $r;
    }

    protected function third_party_connect($info){
        $query_params = "";
        if(isset($info['query_params'])){
            $query_params = "?".http_build_query($info['query_params']);
        }
        $method = $info['method'];

        $full_url = env('KASSATLY_BASE_URL').'/'.$info['action'].$query_params;
        $ch = curl_init();

        $fields = "";
        if(isset($info['params'])){
            $fields = $info['params'];
            $fields = json_encode($fields);
        }
        $headers = isset($info['headers']) ? $info['headers'] : [];
        $http_headers = [];
        foreach ($headers as $key => $header){
            $http_headers[] = $key.": ".$header;
        }

        curl_setopt_array($ch, array(
            CURLOPT_URL => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => $http_headers,
        ));

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $err = curl_errno($ch);
//        dd($full_url,$fields,$response,$http_status,$err);
        curl_close($ch);
        if ($err) {
            return false;
        } else {
            $response = json_decode($response,true);
            if($response == null){
                return false;
            }
            return $response;
        }
    }

    protected function weather_connect($info){
        //$url = env('WEATHER_BASE_URL').'?lat='.$info['latitude'].'&lon='.$info['longitude'].'&format=json&u=c';
        $url = env('WEATHER_BASE_URL');
        $app_id = env('WEATHER_CONSUMER_KEY');
        $consumer_key = env('WEATHER_CONSUMER_KEY');
        $consumer_secret = env('WEATHER_CONSUMER_SECRET');

        $query = array(
            'lat' => $info['latitude'],
            'lon' => $info['longitude'],
            'format' => 'json',
            'u' => 'c'
        );
        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => uniqid(mt_rand(1, 1000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );
        $base_info = $this->buildBaseString($url, 'GET', array_merge($query, $oauth));
        $composite_key = rawurlencode($consumer_secret) . '&';
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;
        $header = array(
            $this->buildAuthorizationHeader($oauth),
            'X-Yahoo-App-Id: ' . $app_id
        );
        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url . '?' . http_build_query($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);

        curl_close($ch);
        if ($err) {
            return false;
        } else {
            $response = json_decode($response);
            if(isset($response->error)){
                return false;
            }
            return $response;
        }
    }

    protected function flick_connect($info){
        $query_params = "";
        if(isset($info['query_params'])){
            $query_params = "?".http_build_query($info['query_params']);
        }
        $method = 'GET';

        $full_url = env('FLICK_BASE_URL').'/'.$info['action'].$query_params;
        $ch = curl_init();

        $fields = "";
        if(isset($info['params'])){
            $fields = $info['params'];
            $fields = json_encode($fields);
        }
        curl_setopt_array($ch, array(
            CURLOPT_URL => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                "Connection: keep-alive",
            ),
        ));
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);

        curl_close($ch);
        if ($err) {
            return false;
        } else {
            $response = json_decode($response,true);
            if($response == null){
                return false;
            }
            return $response;
        }
    }

    public function myfatoorah_connect($info){
        $data = $info['data'];
        $token = env('MYFATOORAH_TOKEN');
        $full_url = env('MYFATOORAH_BASE_URL').$info['function'];

        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $full_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($err) {
                $result['error'] = 'Curl Error: '.$err.' - Failed to Create Session';
            }elseif (!in_array($httpcode, [200])) {
                //$error = json_decode($response)->Message;
                $result['error'] = $response;
            }else{
                $result = json_decode($response,true);
            }
        }catch (\Exception $ex){
            $result['error'] = 'Exception Error: '.$ex->getMessage().' - Failed to Send Request';
        }
        return $result;
    }

    public function payment_connect($info){
        $data = $info['data'];
        $full_url = config('services.arabs.base_url').$info['function'];

        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $full_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($err) {
                $result['error'] = 'Curl Error: '.$err.' - Failed to Create Session';
            }elseif (!in_array($httpcode, [200])) {
                //$error = json_decode($response)->Message;
                $result['error'] = $response;
            }else{
                $result = json_decode($response,true);
            }
        }catch (\Exception $ex){
            $result['error'] = 'Exception Error: '.$ex->getMessage().' - Failed to Send Request';
        }
        return $result;
    }

    protected function clear_cache($info){
        $full_url = env('CACHE_BASE_URL').'/'.$info['method'];
        $ch = curl_init();
        $fields = $info['params'];
//        $fields = json_encode($fields);
        curl_setopt_array($ch, array(
            CURLOPT_URL => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                "Connection: keep-alive",
//                'Authorization: Basic '. base64_encode(env('SMS_USERNAME').":".env('SMS_PASSWORD')),
//                "Content-Type: application/json",
            ),
        ));
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);

        curl_close($ch);
        if ($err) {
            return false;
        } else {
            $response = json_decode($response,true);
            if($response == null){
                return false;
            }
            return $response;
        }
    }

    function correctImageOrientation($filename) {
        if (function_exists('exif_read_data')) {

            $exif = @exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }

    protected function cast_field_value($value,$type){
        $new_value = null;
        switch ($type)
        {
            case $type == 'integer':
                $new_value = intval($value);
                break;
            case $type == 'list':
                $new_value = intval($value);
                break;
            case $type == 'boolean':
                $new_value = (Boolean) $value;
                break;
            case $type == 'string':
                $new_value = strval($value);
                break;
            case $type == 'double':
                $new_value = floatval($value);
                break;
            default:
                $new_value = intval($value);
                break;
        }
        return $new_value;
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function LibanCallSendSMS($message,$msisdn,$sender,$lang='en',$is_leb=true)
    {
        //sender max 11 digits
        //lang => 1: arabic , 2: english
        $lang = $lang == 'en' ? 2 : 1;
        $is_leb = $is_leb ? 1 : 0;
        $url = env('SMS_BASE_URL');
        $data = ['username' => env('SMS_USERNAME'), 'password' => env('SMS_PASSWORD'), 'phone' => $msisdn, 'codeno' => 1, 'message' => $message, 'lang' => $lang, 'isleb' => $is_leb, 'code' => $sender];

        $options = array(
            'http' => array(
                'method' => 'GET'
            )
        );

        $url .= '?' . http_build_query($data);
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */
            return false;
        }
        return true;
    }

    public function SendQpaySMS($Body,$Nbr,$title)
    {
        $url = env('SMS_BASE_URL');
        $data = '?uid='.env('SMS_USERNAME').'&pwd='.env('SMS_PASSWORD').'&src='.$title.'&dst='.$Nbr.'&msg='.rawurlencode($Body);
        $options = array(
            'http' => array(
                'method' => 'GET'
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url.$data);
        if($response != '+OK'){
            return false;
        }
        return true;
    }

    function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {
        $IMAGE_HANDLERS = [
            '2' => [
                'load' => 'imagecreatefromjpeg',
                'save' => 'imagejpeg',
                'quality' => 100
            ],
            '3' => [
                'load' => 'imagecreatefrompng',
                'save' => 'imagepng',
                'quality' => 0
            ],
            '1' => [
                'load' => 'imagecreatefromgif',
                'save' => 'imagegif',
            ]
        ];
        // 1. Load the image from the given $src
        // - see if the file actually exists
        // - check if it's of a valid image type
        // - load the image resource
        // get the type of the image
        // we need the type to determine the correct loader
        $type = exif_imagetype($src);
        if($type == 1 || $type == 6){
            return null;
        }
        // if no valid type or no handler found -> exit
        if (!$type || !$IMAGE_HANDLERS[$type]) {
            return null;
        }
        // load the image with the correct loader
        try{
            $image = call_user_func($IMAGE_HANDLERS[$type]['load'], $src);
        }catch (\Exception $exception){
            return null;
        }

        // no image found at supplied location -> exit
        if (!$image) {
            return null;
        }
        // 2. Create a thumbnail and resize the loaded $image
        // - get the image dimensions
        // - define the output size appropriately
        // - create a thumbnail based on that size
        // - set alpha transparency for GIFs and PNGs
        // - draw the final thumbnail
        // get original image width and height
        $width = imagesx($image);
        $height = imagesy($image);
        // maintain aspect ratio when no height set
        if ($targetHeight == null) {
            // get width to height ratio
            $ratio = $width / $height;
            // if is portrait
            // use ratio to scale height to fit in square
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            }
            // if is landscape
            // use ratio to scale width to fit in square
            else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }
        // create duplicate image based on calculated target size
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
        // set transparency options for GIFs and PNGs
        if ($type == 1 || $type == 3) {
            // make image transparent
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );
            // additional settings for PNGs
            if ($type == 3) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }
        // copy entire source image to duplicate image and resize
        imagecopyresampled(
            $thumbnail,
            $image,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $width, $height
        );
        // 3. Save the $thumbnail to disk
        // - call the correct save method
        // - set the correct quality level
        // save the duplicate version of the image to disk
        return call_user_func(
            $IMAGE_HANDLERS[$type]['save'],
            $thumbnail,
            $dest,
            $IMAGE_HANDLERS[$type]['quality']
        );
    }


    public static function redirect($uri = '', $payment_data=array())
    {
        if ( ! preg_match('#^(\w+:)?//#i', $uri))
        {
            $uri = url();
        }
        $action_url = $uri . "?";
        $str = 0;

        foreach ($payment_data as $key => $value) {
            if (strlen($value) > 0) {
                if ($str == 0) {
                    if(!is_null($value)) {
                        $action_url .= urlencode( $key ) . '=' . urlencode( $value );
                        $str = 1;
                    }
                } else {
                    if(!is_null($value)) {
                        $action_url .= '&' . urlencode($key) . "=" . urlencode($value);
                    }
                }
            }
        }

        header('Location: '.$action_url, TRUE);
        exit();
    }

    public function sign ($params) {
        return $this->signData($this->buildDataToSign($params), env('PAYMENT_SECRET_KEY'));
    }

    public function signData($data, $secretKey) {
        return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
    }

    public function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
            $dataToSign[] = $field . "=" . $params[$field];
        }
        return $this->commaSeparate($dataToSign);
    }

    public function commaSeparate ($dataToSign) {
        return implode(",",$dataToSign);
    }

    public function generate_tokens($length){
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        if($length == 4){
            return sprintf('%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535));
        }else{
            $token = '';
            for($i=0;$i<$length;$i=$i+4){
                $token .= sprintf('%04X', mt_rand(0, 65535));
            }
            return $token;
        }
    }

    public static function number_generator($length = 4){
        return  rand(pow(10, $length-1), pow(10, $length)-1);
    }

    function validate_player_id($player_id){
        //12345678-1234-1234-1234-123456789123
        $chunks = explode('-',$player_id);
        if(count($chunks) == 5 && strlen($chunks[0]) == 8 && strlen($chunks[1]) == 4 && strlen($chunks[2]) == 4 && strlen($chunks[3]) == 4 && strlen($chunks[4]) == 12){
            return True;
        }
        return False;
    }

    public function manage_session($info){
        $data = $info['data'];
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYMENT_SESSION_BASE_URL'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($err) {
                $result['error'] = 'Curl Error: '.$err.' - Failed to Create Session';
            }elseif (!in_array($httpcode, [200])) {
                $error = json_decode($response)->message;
                $result['error'] = $error;
            }else{

                $result = $response;
            }
        }catch (\Exception $ex){
            $result['error'] = 'Exception Error: '.$ex->getMessage().' - Failed to Send Request';
        }
        return $result;
    }

    protected function send_otp($phoneNumber,$otp)
    {
        // Log::info($phoneNumber);
        // Log::info($otp);
        $message = 'Please verify using the following code: '.$otp;
        $senderId = 'Barterdom';

        $apiUrl = env('SMSGLOBAL_API_URL');
        $apiKey = env('SMSGLOBAL_API_KEY');
        $secretKey = env('SMSGLOBAL_SECRET_KEY');

        $ts = time();
        $nonce = Str::random(8);
        $urlComponents = parse_url($apiUrl);

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
                // Log::info('SMS sent successfully');
                return response()->json(['message' => 'SMS sent successfully']);
            } else {
                // Handle error if needed
                // Log::info('Failed to send SMS');
                return response()->json(['error' => 'Failed to send SMS']);
            }
        } catch (\Exception $e) {
            // Handle exceptions, log errors, etc.
            return response()->json(['error' => 'Error sending SMS: ' . $e->getMessage()]);
        }
    }

    function oneSignalViewUser($player_id){
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('ONE_SIGNAL_USER_URL').'/'.$player_id.'?app_id='.env('ONE_SIGNAL_APP_ID'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $err = curl_errno($ch);
            curl_close($ch);
            if ($err) {
                $result['error'] = 'Curl Error: '.$err.' - Failed to Send Push';
            } else {
                if($http_status != 200){
                    $response = json_decode($response);
                    $result['error'] = $response->errors[0];
                }else{
                    $result = json_decode($response,true);
                }
            }
        }catch (\Exception $ex){
            $result['error'] = 'Exception Error: '.$ex->getMessage().' - Failed to Send Push';
        }
        return $result;
    }

    function oneSignal($info){
        $type = isset($info['type']) ? $info['type'] : 'user';
        $contents = $info['contents'];
        $headings = $info['headings'];
        $player_ids = $info['player_ids'];
        $included_segments = !isset($info['included_segments']) ? [] : $info['included_segments'];
        $excluded_segments = !isset($info['excluded_segments']) ? [] : $info['excluded_segments'];
        $data = isset($info['data']) ? $info['data'] : [];
        $result = [];
        $app_id = $type == 'user' ? env('ONE_SIGNAL_APP_ID') : env('ONE_SIGNAL_MERCHANT_APP_ID');
        $android_channel_id = $type == 'user' ? env('ONE_SIGNAL_CHANNEL_ID') : env('ONE_SIGNAL_MERCHANT_CHANNEL_ID');
        $fields = array(
            'app_id' => $app_id,
            'android_channel_id' => $android_channel_id,
            'include_player_ids' => $player_ids,
            'included_segments' => $included_segments,
            'excluded_segments' => $excluded_segments,
            'contents' => $contents,
            'headings' => $headings,
//            'send_after' => Carbon::now('UTC')->addDays(1)->format('Y-m-d H:i:s'),
//            'send_after' => Carbon::now('UTC')->addHours(2)->format('Y-m-d H:i:s'),
        );
        if(isset($info['send_after'])){
            $fields['send_after'] = $info['send_after'];
        }
        if(!empty($info['data'])){
            $fields['data'] = $info['data'];
        }
        if(isset($info['big_picture'])){
            $fields['big_picture'] = $info['big_picture'];
        }
        if(isset($info['ios_attachments'])){
            $fields['ios_attachments'] = $info['ios_attachments'];
        }
        if(isset($info['ios_badgeType'])){
            $fields['ios_badgeType'] = $info['ios_badgeType'];
        }
        if(isset($info['ios_badgeCount'])){
            $fields['ios_badgeCount'] = $info['ios_badgeCount'];
        }
        $fields = json_encode($fields);
        try{
            $ch = curl_init();
            $rest_api_key = $type == 'user' ? env('ONE_SIGNAL_REST_API_KEY') : env('ONE_SIGNAL_MERCHANT_REST_API_KEY');
            curl_setopt($ch, CURLOPT_URL, env('ONE_SIGNAL_URL'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                'Authorization: Basic '.$rest_api_key));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_errno($ch);
            curl_close($ch);
            if ($err) {
                $result['error'] = 'Curl Error: '.$err.' - Failed to Send Push';
            } else {
                $result = $this->parse_push_response($response);
            }
        }catch (\Exception $ex){
            $result['error'] = 'Exception Error: '.$ex->getMessage().' - Failed to Send Push';
        }
        return $result;
    }

    public function parse_push_response($response){
        $response = json_decode($response,true);
        $response = (array) $response;
        $data = [];
        $data['status'] = '';
        $data['message'] = '';
        $data['debugger'] = '';
        if(isset($response['id'])){
            if(!empty($response['id'])){//200 ok
                $data['status'] = 'OK';
                $data['message'] = 'Push Sent Successfully to all devices';
                $data['debugger'] = 'Push Sent Successfully to all devices';
            }else{//200 no subscribed players
                $data['status'] = 'OK_WITH_ERRORS';
                if(isset($response['errors'])){
                    $data['message'] = isset($response['errors'][0]) ? $response['errors'][0] : 'All included players are not subscribed';
                    $data['debugger'] = isset($response['errors'][0]) ? $response['errors'][0] : 'All included players are not subscribed';
                }
            }
        }else{

            if(isset($response['errors'])){
                if(!empty($response['errors']['invalid_player_ids'])){//200 invalid player ids
                    $data['status'] = 'OK_WITH_ERRORS';
                    $data['message'] = 'Some Users are Invalid';
                    $data['debugger'] ='Some Users are Invalid';
                }else{//400
                    $data = [];
                    $data['error'] = isset($response['errors'][0]) ? $response['errors'][0] : 'Invalid Notification Content';
                }
            }else{//400
                $data = [];
                $data['error'] = 'Unknown Error';
            }
        }
        return $data;
    }

	function return_json($data = array(), $status = 200) {
		return response()->json($data)->setStatusCode($status);
	}


    public function getExtension($filename)
    {
        $extension = false;
        $extension = File::mimeType($filename);
        return $extension;
    }

    public function setLang($language){
        App::setLocale($language);
        $this->lang = $language;
         return $language;
    }

    public function getCategories($info){
        $enabled_categories = [];
        $enabled_categories[] = ['id' => 1,'name' => 'Roller shutters'];
        $enabled_categories[] = ['id' => 4,'name' => 'Internal blinds'];
        $enabled_categories[] = ['id' => 5,'name' => 'Curtains'];
        $enabled_categories[] = ['id' => 6,'name' => 'Gates'];
        $enabled_categories[] = ['id' => 7,'name' => 'Garage doors'];
        $enabled_categories[] = ['id' => 8,'name' => 'Alarms'];
        $enabled_categories[] = ['id' => 10,'name' => 'Cameras'];
        $enabled_categories[] = ['id' => 11,'name' => 'Video door phones'];
        $enabled_categories[] = ['id' => 12,'name' => 'Awnings'];
        $enabled_categories[] = ['id' => 19,'name' => 'Pergola'];
        $enabled_categories[] = ['id' => 21,'name' => 'Home automation'];
        $enabled_categories[] = ['id' => 22,'name' => 'Connexoon'];
        $enabled_categories[] = ['id' => 24,'name' => 'Light management'];
        $enabled_categories[] = ['id' => 30,'name' => 'Heat management'];
        $enabled_categories[] = ['id' => 31,'name' => 'Swinging shutter'];
        $enabled_categories[] = ['id' => 38,'name' => 'Industrial & commercial access '];

        $site_id = $info['site_id'];
        $lang_code = $info['lang_code'];
        $filter_group = $info['filter_group'];

        $url = $this->categories_url . '&siteId=' . $site_id . $this->site_code . '&language=' . $lang_code . '&filterGroup=' . $filter_group;
        $response = file_get_contents($url);
        $response = json_decode($response);
        $applications = $response->APPLICATIONS;
        $categories = [];
        foreach ($enabled_categories as $category) {
            $c_id = $category['id'];
            $c_name = $category['name'];
            if(!isset($applications->$c_id)){
                $categories[] = [
                    'id' => $c_id,
                    'name' => $c_name,
                    'label' => $c_name,
                    'cover_image' => secure_url('/public/images/'.$c_id.'.jpg')
                ];
            }else{
                $categories[] = [
                    'id' => $applications->$c_id->id,
                    'name' => $applications->$c_id->name,
                    'label' => $applications->$c_id->name,
                    'cover_image' => secure_url('/public/images/'.$applications->$c_id->id.'.jpg')
                ];
            }
        }
        return $categories;
    }

    public function fillTypes($info){
        $site_id = $info['site_id'];
        $lang_code = $info['lang_code'];
        $filter_group = $info['filter_group'];
        $categories = $info['categories'];

        $url = $this->types_url . '&siteId=' . $site_id . $this->site_code . '&language=' . $lang_code . '&filterGroup=' . $filter_group;
        $response = file_get_contents($url);
        $response = json_decode($response);
        $types = $response->PRODUCTSTYPES;
        $types = json_decode(json_encode($types), true);
        $types = array_values($types);
        foreach ($types as $i=>$type){
            $types[$i]['products'] = [];
        }
        foreach ($categories as $index => $category){
            $categories[$index]['types'] = $types;
        }
        return $categories;
    }

    public function getSiteInfo($data){
	    $countries = [];
	    $countries['Algeria'] = ['site_id' => 'AC','filter_group' => 'alge-mobile'];
	    $countries['Central Africa'] = ['site_id' => 'AC','filter_group' => 'ceaf-mobile'];
	    $countries['Angola'] = ['site_id' => 'AC','filter_group' => 'ceaf-mobile'];
	    $countries['Ethiopia'] = ['site_id' => 'AC','filter_group' => 'ceaf-mobile'];
	    $countries['Ivor Coast'] = ['site_id' => 'AC','filter_group' => 'ceaf-mobile'];
	    $countries['Cameroon'] = ['site_id' => 'AC','filter_group' => 'ceaf-mobile'];
	    $countries['Gabon'] = ['site_id' => 'AC','filter_group' => 'ceaf-mobile'];
	    $countries['Cyprus'] = ['site_id' => 'LB','filter_group' => 'cypr-mobile'];
	    $countries['Egypt'] = ['site_id' => 'AC','filter_group' => 'egyp-mobile'];
	    $countries['Jordan'] = ['site_id' => 'LB','filter_group' => 'jord-mobile'];
	    $countries['Lebanon'] = ['site_id' => 'LB','filter_group' => 'leba-mobile'];
	    $countries['Tunisia'] = ['site_id' => 'AC','filter_group' => 'tuni-mobile'];
	    $countries['Morocco'] = ['site_id' => 'MA','filter_group' => 'moro-mobile'];
	    $countries['South Africa'] = ['site_id' => 'ZA','filter_group' => 'soaf-mobile'];
	    $countries['Turkey'] = ['site_id' => 'TR','filter_group' => 'turk-mobile'];
	    $countries['Azerbaijan'] = ['site_id' => 'TR','filter_group' => 'azer-mobile'];
	    $countries['Kuwait'] = ['site_id' => 'GC','filter_group' => 'kuwa-mobile'];
	    $countries['Saudi Arabia'] = ['site_id' => 'GC','filter_group' => 'ksam-mobile'];
	    $countries['United Arab Emirates'] = ['site_id' => 'GC','filter_group' => 'uaem-mobile'];
	    $countries['Bahrain'] = ['site_id' => 'GC','filter_group' => 'bahr-mobile'];
	    $countries['Iran'] = ['site_id' => 'GC','filter_group' => 'iran-mobile'];
	    $country_name = $data['country_name'];
        if(!array_key_exists($country_name, $countries)) {
	        return false;
        }
	    return $countries[$country_name];
    }

    public static function getSocialSecuritySectionName($id){
        $sections = [];
        $sections['1'] = 'تحقيق إجتماعي';
        $sections['2'] = 'تعويض نهاية الخدمة';
        $sections['3'] = 'موافقات طبية';
        $sections['4'] = 'زواج';
        $sections['5'] = 'ولادات';
        $sections['6'] = 'وفاة';
        $sections['7'] = 'حوادث العمل';
        $sections['8'] = 'ألتأمين على الحياة';
        if(!isset($sections[$id])){
            return $sections['1'];
        }
        return $sections[$id];
    }

    public static function getMemoSectionName($id){
        $sections = [];
        $sections['1'] = 'CHAIRMAN';
        $sections['2'] = 'HUMAN RESOURCES';
        $sections['3'] = 'FINANCE';
        $sections['4'] = 'GAMING';
        $sections['5'] = 'SLOTS';
        $sections['6'] = 'POKER';
        $sections['7'] = 'ENTERTAINMENT';
        $sections['8'] = 'FOOD & BEVERAGE';
        $sections['9'] = 'MARKETING';
        $sections['10'] = 'PURCHASING';
        $sections['11'] = 'INFORMATION TECHNOLOGY';
        $sections['12'] = 'SECURITY';
        if(!isset($sections[$id])){
            return $sections['1'];
        }
        return $sections[$id];
    }

    public static function getDayName($id){
        $sections = [];
        $sections['1'] = 'Monday';
        $sections['2'] = 'Tuesday';
        $sections['3'] = 'Wednesday';
        $sections['4'] = 'Thursday';
        $sections['5'] = 'Friday';
        $sections['6'] = 'Saturday';
        $sections['7'] = 'Sunday';
        if(!isset($sections[$id])){
            return $sections['1'];
        }
        return $sections[$id];
    }

    public static function read_employees($type,$item_id){
        $response = [];
        $response['read_number'] = UserPdf::whereItemId($item_id)->whereType($type)->count();
        $response['total_number'] = Users::count();
        $response['read_users'] = UserPdf::whereItemId($item_id)->whereType($type)->get();
        return $response;
    }

    public static function getImportantNoticeSectionName($id){
        $sections = [];
        $sections['1'] = 'CHAIRMAN';
        $sections['2'] = 'HUMAN RESOURCES';
        $sections['3'] = 'FINANCE';
        $sections['4'] = 'GAMING';
        $sections['5'] = 'SLOTS';
        $sections['6'] = 'POKER';
        $sections['7'] = 'ENTERTAINMENT';
        $sections['8'] = 'FOOD & BEVERAGE';
        $sections['9'] = 'MARKETING';
        $sections['10'] = 'PURCHASING';
        $sections['11'] = 'INFORMATION TECHNOLOGY';
        $sections['12'] = 'SECURITY';
        if(!isset($sections[$id])){
            return $sections['1'];
        }
        return $sections[$id];
    }

	public function return_error($debugger = NULL, $code = 1, $message = NULL) {

		$error_data['error'] = array();
		$error_data['error']['message']     = utf8_decode(utf8_encode(Lang::get($message)));
		$error_data['error']['code']        = $code;
		$error_data['error']['debugger']    = $debugger;

		return response()->json($error_data)->setStatusCode(400);
	}

	protected function build_account($user_data, $update = false) {
		$account_data = array(
			'FirstName'                 => $user_data['first_name'],
			'LastName'                  => $user_data['last_name'],
			'Salutation'                => $user_data['salutation'],
			'Country_of_Residence__c'   => $user_data['country'],
			'Date_of_Birth__c'          => $user_data['date_of_birth'],
			'BillingStreet'             => $user_data['address'],
			'BillingCountry'            => $user_data['country'],
			'ShippingStreet'            => $user_data['address'],
			'ShippingCountry'           => $user_data['country'],
			'Phone'                     => $user_data['number']
		);


		if(!$update) {
			$account_data['Type'] = "Member";
			$account_data['RecordTypeId'] = "01220000000G802AAC";
		}

		if(isset($user_data['membership'])) {
			$account_data['Membershipp__pc'] = $user_data['membership'];
		}

		if(isset($user_data['email'])) {
			$account_data['PersonEmail'] = $user_data['email'];
		}

		if(isset($user_data['marital_status'])) {
			$account_data['Marital_Status__c'] = $user_data['marital_status'];
		}

		$account_data['attributes']['type'] = "Account";

		return $account_data;
	}

	protected function valid_access_token(Request $request) {
		$access_token = $request->header("token");

		if(isset($access_token) && $access_token != "") {
			return $access_token;
		} else {
			return false;
		}
	}

    protected function valid_language(Request $request) {
        $language_id = $request->header("lang");
        $language = CountryLanguages::where('id',$language_id)->first();

        if(isset($language_id) && $language_id != "" && $language) {
            $lang = $this->setLang($language);
            return $language;
        } else {
            return false;
        }
    }

	protected function valid_accesstoken(Request $request) {
		$access_token = $request->header("token");

		if(isset($access_token) && $access_token != "") {
			$user_id = UsersAccess::where("access_token", "=", $access_token)->first();

			if($user_id) {
				$user_id = $user_id->user_id;
				return Users::where("id", "=", $user_id)->first();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

    protected function get_user_by_access_token($access_token) {
        $user_id = UsersAccess::where("access_token", "=", $access_token)->first();

        if($user_id) {
            if($user_id->user_id == 1714){
                return Users::where("id", "=", $user_id->user_id)->first();
            }
            if($user_id->expired_at->isPast()){
                return false;
            }
            /**
             * refresh token
             */
            $user_id->expired_at = Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s');
            $user_id->save();
            $user_id = $user_id->user_id;
            return Users::where("id", "=", $user_id)->first();
        } else {
            return false;
        }
    }

	public function get_guid($length = 8) {
		mt_srand((double)microtime()*10000);
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$uuid = substr($charid, 0, $length);

		return $uuid;
	}

	public function parseCategories($categories,$data){
        foreach ($data as $item){
            if(count($categories) > 0){
                $found = false;
                foreach ($categories as $category){
                    //if($category->label != $item->label || $category->name != $item->name || $category->id != $item->id){
                    if($category['label'] == $item->label && $category['name'] == $item->name && $category['id'] == $item->id){
                        $found = true;
                    }
                }
                if(!$found){
                    $categories[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'label' => $item->label
                    ];
                }
            }else{
                $data = json_decode(json_encode($data), True);
                $categories = $data;
            }
        }
        return $categories;
    }

    public function checkType($category_types,$type){
	    $found = false;
        foreach ($category_types as $c_type){
            if($c_type['label'] == $type['label'] && $c_type['name'] == $type['name'] && $c_type['id'] == $type['id']){
                $found = true;
            }
        }
        return $found;
    }
}

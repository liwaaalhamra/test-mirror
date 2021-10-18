<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Api\Users;
use App\Models\Api\UsersAccess;
use Carbon\Carbon;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use GuzzleHttp\Client;

class AppleController extends Controller
{
	public $client_id;
	public $key_id;
	public $team_id;
	public $state;
	public $private_key;
	public $redirect_url;

	public function __construct()
	{
		$this->private_key = env('APPLE_PRIVATE_KEY');
		$this->client_id = env('APPLE_CLIENT_ID');
		$this->key_id = env('APPLE_KEY_ID');
		$this->team_id = env('APPLE_TEAM_ID');
		$this->state = env('APPLE_STATE');
		$this->redirect_url = secure_url('apple/redirect');
	}

	public function index()
	{
		// Page Title
		$page_title = 'Apple Login';
		$redirect_url = $this->redirect_url;

		return view('auth.apple', compact(
			'page_title','redirect_url'
		));
	}

	public function apple_redirect(Request $request)
	{
		# The Request will return state, code, id_token
		# The user object would be returned only for the first time.

		# State Validation
		if($this->state != $request->state){
			abort(500);
		}

		# ID Token (id_token) Validation
		$tokenParts = explode(".", $request['id_token']);
		$tokenHeader = base64_decode($tokenParts[0]);
		$tokenPayload = base64_decode($tokenParts[1]);
		$jwtHeader = json_decode($tokenHeader, true);
		$jwtPayload = json_decode($tokenPayload, true);

		// # Verify token
		// # Before we use the token, we need to make sure that it was signed by Apple's private key.
		// # To do that, we need Apple's public key to verify the signature.
		// $client = new Client();
		// $response = $client->request('GET', 'https://appleid.apple.com/auth/keys');
		// $private_keys = json_decode($response->getBody(), true)['keys'];

		// # Validate Private Key
		// $apple_private_key = [];
		// foreach($private_keys as $private_key){
		// 	if($private_key['kid'] == $jwtHeader['kid']){
		// 		$apple_private_key = $private_key;
		// 	}
		// }

		$generateToken = $this->generateToken($request->code);

		if($generateToken){

            $agent = new Agent();
            $device_name = $agent->device();
            $browser_name = $agent->browser();
            $browser_version = $agent->version($agent->browser());
            $platform_name = $agent->platform();
            $platform_version = $agent->version($agent->platform());
            $is_mobile = $agent->isMobile();
            $is_tablet = $agent->isTablet();
            $is_desktop = $agent->isDesktop();
            $ip = isset($agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR']) ? $agent->getHttpHeaders()['HTTP_X_FORWARDED_FOR'] : null;
            $user_request = json_decode($request['user'], true);
            $user = Users::updateOrCreate(['apple_id' => $jwtPayload['sub']],
                [
                    'account_type' => 'email',
                    'email' => $user_request['email'],
                    'is_verified' => 1,
                    'verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'registration' => null,
                    'pin' => null,
                    'device_name' => $device_name,
                    'browser_name' => $browser_name,
                    'browser_version' => $browser_version,
                    'platform_name' => $platform_name,
                    'platform_version' => $platform_version,
                    'is_mobile' => $is_mobile,
                    'is_tablet' => $is_tablet,
                    'is_desktop' => $is_desktop,
                    'user_agent' => $agent->getUserAgent(),
                    'ip' => $ip,
                ]);
            if($user->is_blocked){
                return parent::return_error("Account Blocked", 407, "Account Blocked");
            }
            UsersAccess::where("user_id", "=", $user->id)->delete();
            $access_token = str_random(64);
            UsersAccess::create([
                'access_token' => $access_token,
                'user_id' => $user->id,
                'expired_at' => Carbon::now()->addMinutes(Config::get('session.api_lifetime'))->format('Y-m-d H:i:s')

            ]);
            $user->token = $access_token;

		}

		return redirect()->route('apple.completed', ['token' => $access_token]);
	}

	public function completed(){

	}

	public function generateClientSecret()
	{
		$headers = [
			'kid' => $this->key_id,
			"alg" => "ES256",
		];

		$payload = [
			"iss" => $this->team_id,
			"iat" => time(),
			"exp" => time() + 86400*180,
			"aud" => "https://appleid.apple.com",
			'sub' => $this->client_id,
		];

        $str = $this->private_key;
        $str = chunk_split($str, 64, "\n");
        $key = "-----BEGIN RSA PRIVATE KEY-----\n$str-----END RSA PRIVATE KEY-----\n";

		$client_secret = JWT::encode($payload, $key, 'ES256');

		return $client_secret;
	}

	# Validate an authorization grant code delivered to your app to obtain tokens,
	# or validate an existing refresh token.
	public function generateToken($code)
	{
		$client_secret = $this->generateClientSecret();

		$client = new Client();
		$response = $client->request('POST', 'https://appleid.apple.com/auth/token', [
			'form_params' => [
				'client_id' => $this->client_id,
				'client_secret' => $client_secret,
				'code' => $code,
				'grant_type' => 'authorization_code',
				'redirect_uri' => $this->redirect_url
			]
		]);

		$response = json_decode($response->getBody(), true);

		return $response;
	}

}

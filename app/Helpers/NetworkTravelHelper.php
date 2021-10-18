<?php

namespace App\Helpers;

use App\Models\Api\RecordTypeMapping;
use App\Models\Api\StatusMapping;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class NetworkTravelHelper {

	private   $api_url      = "https://cs18.salesforce.com/services/data/v35.0/";

	protected $access_token = "";
	protected $response;
	protected $error;

	/**
	 * NetworkTravelHelper constructor.
	 *
	 * @param string $access_token
	 */
	public function __construct( $access_token ) {
		$this->access_token = $access_token;
	}

	function create_request($request_details) {
		
		$client = new Client([
			'base_uri'  => $this->api_url . "sobjects/Case/",
			'headers'   => [
				"Authorization" => "Bearer $this->access_token",
				"Content-Type"  => "application/json"
			],
			'json'      => $request_details
		]);

		try {
			$request = $client->request('POST');

			$response = json_decode((string) $request->getBody());
		}  catch (ClientException $e) {
			$response = $e->getResponse();

			if($response->getStatusCode() >= 400) {
				$this->error = json_decode((string) $response->getBody());

				return false;
			}
		}

		$this->response = $response;

		return true;
	}

	function update_request($request_details, $request_id) {

		$client = new Client([
			'base_uri'  => $this->api_url . "sobjects/Case/" . $request_id,
			'headers'   => [
				"Authorization" => "Bearer $this->access_token",
				"Content-Type"  => "application/json"
			],
			'json'      => $request_details
		]);

		try {
			$request = $client->request('PATCH');

			$response = json_decode((string) $request->getBody());
		}  catch (ClientException $e) {
			$response = $e->getResponse();

			if($response->getStatusCode() >= 400) {
				$this->error = json_decode((string) $response->getBody());

				return false;
			}
		}

		$this->response = $response;

		return true;
	}

	/**
	 * @return string
	 */
	public function getAccessToken() {
		return $this->access_token;
	}

	/**
	 * @return mixed
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @return mixed
	 */
	public function getResponse() {
		return $this->response;
	}

}
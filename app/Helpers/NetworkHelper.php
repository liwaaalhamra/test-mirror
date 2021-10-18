<?php

namespace App\Helpers;

use App\Models\Api\RecordTypeMapping;
use App\Models\Api\StatusMapping;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class NetworkHelper
{

    private $api_url = "https://cs18.salesforce.com/services/data/v35.0/";

    protected $access_token = "";
    protected $response;
    protected $error;

    function sf_login()
    {
        $client = new Client([
            'base_uri' => "https://test.salesforce.com/services/oauth2/token",
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '3MVG98RqVesxRgQ6XIlRRxBWtdQgcZZSrdQPzEujgY.BVmclNEvJiJANuVfZAUdRCz0eANxy3KgCvdSO.fyCc',
                'client_secret' => '2146288793036740075',
                'username' => 'android@s-signature.com.dev',
                'password' => 'Appteam1011eZ76dlG5FaLwvQtUYqtY4vvx'
            ]
        ]);

        try {
            $request = $client->request('POST');
            $response = json_decode((string)$request->getBody());

        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;
        $this->access_token = $response->access_token;

        return true;
    }

    function account_exist($email)
    {
        $client = new Client([
            'base_uri' => $this->api_url . "query",
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'query' => [
                "q" => "SELECT Account.name FROM Account WHERE PersonEmail='$email'"
            ],
        ]);

        try {
            $request = $client->request('GET');
            $response = json_decode((string)$request->getBody());

        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function account_profile($account)
    {
        $client = new Client([
            'base_uri' => $this->api_url . "sobjects/Account/" . $account,
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ]
        ]);

        try {
            $request = $client->request('GET');
            $response = json_decode($request->getBody());

        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function create_profile($user_account)
    {

        $client = new Client([
            'base_uri' => $this->api_url . "sobjects/Account/",
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'json' => $user_account
        ]);

        try {
            $request = $client->request('POST');

            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function update_profile($user_id, $user_account)
    {
        $client = new Client([
            'base_uri' => $this->api_url . "sobjects/Account/" . $user_id,
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'json' => $user_account
        ]);

        try {
            $request = $client->request('PATCH');
            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function update_request($request_id, $request_data)
    {
        $client = new Client([
            'base_uri' => $this->api_url . "sobjects/Case/" . $request_id,
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'json' => $request_data
        ]);

        try {
            $request = $client->request('PATCH');

            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function get_requests($user_id, $list_of_record_types)
    {

        $where_query = '';
        foreach ($list_of_record_types as $list_of_record_type) {
            if ($where_query == '') {
                $where_query .= "(RecordTypeId='" . $list_of_record_type->record_type_id . "' ";
            } else {
                $where_query .= "OR RecordTypeId='" . $list_of_record_type->record_type_id . "' ";
            }
        }

        $where_query .= ")";

        $query = "SELECT id, due_date__c, category__c, createddate, subject, action__c, recordtypeid, payment_status__c, status, member_balance_due__c FROM case WHERE accountId='$user_id' AND $where_query";

        $client = new Client([
            'base_uri' => $this->api_url . "query",
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'query' => [
                "q" => $query
            ],
        ]);

        try {
            $request = $client->request('GET');

            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function get_all_requests($user_id)
    {
        $client = new Client([
            'base_uri' => $this->api_url . "query",
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'query' => [
                "q" => "SELECT id, due_date__c, category__c, createddate, Request_RT__c, subject, action__c, recordtypeid, payment_status__c, status, member_balance_due__c FROM case WHERE accountId='$user_id'"
            ],
        ]);

        try {
            $request = $client->request('GET');

            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function get_single_request($request_id)
    {
        $client = new Client([
            'base_uri' => $this->api_url . "sobjects/Case/" . $request_id,
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ]
        ]);

        try {
            $request = $client->request('GET');

            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }


    function get_dashboard_statistics($user_id, $condition, $condition_param)
    {

        $query = "";
        switch ($condition) {
            case "status":
                $query = "SELECT id FROM case WHERE accountId='$user_id' AND status = '$condition_param'";
                break;
            case "payment_status":
                $query = "select SUM(Member_Balance_Due__c) from case WHERE accountId='$user_id'";
                break;
            case "experience":
                $query = "select COUNT(id) from case WHERE accountId='$user_id' AND RecordTypeId='$condition_param'";
                break;
            case "case":
                $date = new \DateTime();
                $current_time = $date->format("Y-m-d H:i:s");
                $query = "select COUNT(id) from case WHERE accountId='$user_id' AND due_date__c < '$current_time'";
                break;
        }

        $client = new Client([
            'base_uri' => $this->api_url . "query",
            'headers' => [
                "Authorization" => "Bearer $this->access_token"
            ],
            'query' => [
                "q" => $query
            ],
        ]);

        try {
            $request = $client->request('GET');

            $response = json_decode((string)$request->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 400) {
                $this->error = json_decode((string)$response->getBody());

                return false;
            }
        }

        $this->response = $response;

        return true;
    }

    function get_dashboard($user_id)
    {
        $statuses = StatusMapping::all();

        $return_data["status"] = array();

        foreach ($statuses as $status) {
            $stat["name"] = $status->title;
            self::get_dashboard_statistics($user_id, "status", $status->title);
            $stat["total"] = self::getResponse()->totalSize;
            $stat["hex"] = $status->hex;
            array_push($return_data["status"], $stat);
        }

        self::get_dashboard_statistics($user_id, "payment_status", "Unpaid");
        $return_data["pending_balance"] = self::getResponse()->records[0]->expr0;

        $experiences = RecordTypeMapping::all();

        $return_data["experience"] = array();
        foreach ($experiences as $experience) {
            if ($experience->belongs_to == 0) {
                self::get_dashboard_statistics($user_id, "experience", $experience->record_type_id);
                $exp["id"] = $experience->id;
                $exp["name"] = $experience->record_type_name;
                $exp["total"] = self::getResponse()->records[0]->expr0;
                $exp["hex"] = $experience->hex;
                array_push($return_data["experience"], $exp);
            } else {
                $exps = $return_data["experience"];
                for ($i = 0; $i <= count($exps); $i++) {
                    if ($exps[$i]['id'] == $experience->belongs_to) {
                        self::get_dashboard_statistics($user_id, "experience", $experience->record_type_id);
                        $exps[$i]['total'] += self::getResponse()->records[0]->expr0;
                        break;
                    }
                }
                $return_data["experience"] = $exps;

            }
        }

        self::get_dashboard_statistics($user_id, "case", "upcoming");
        $return_data["upcoming"] = self::getResponse()->records[0]->expr0;
        $return_data["notifications"] = 0;

        return $return_data;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}
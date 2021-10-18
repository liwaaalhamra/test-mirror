<?php

namespace App\Helpers;

use App\Models\Api\RecordRequestMapping;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\SalesRequests;
use App\Models\Api\StatusMapping;
use Carbon\Carbon;

class ResponseHelper {

	protected $access_token = "";
	protected $response;
	protected $error;
	private $renew_payment = false;
	public $months_to_pay = array();
	public $first_payment = false;

	public $MONTHLY_PAYMENT             = 1;
	public $MONTHLY_PAYMENT_TEXT        = 'Monthly';
	public $SEMI_ANNUALLY_PAYMENT       = 2;
	public $SEMI_ANNUALLY_PAYMENT_TEXT  = 'Semi-Annual';
	public $ANNUAL_PAYMENT              = 3;
	public $ANNUAL_PAYMENT_TEXT         = 'Annually';

	public $LOCAL_MEMBERSHIP            = 1;
	public $INTERNATIONAL_MEMBERSHIP    = 2;
	public $SIGNATURE_MEMBERSHIP        = 3;

	public $record_type_mapping_fields = array(
		'01220000000GK9HAAW' => [
			'RT_Pick_Up_Date_Time__c',
			'RT_Pick_up_Location__c',
			'Drop_Off_Date_Time__c',
			'RT_Drop_Off_Location__c',
			'Date_of_Return__c',
			'RT_Vehicule__c',
			'Due_Date__c',
			'Category__c',
			'Request__c',
			'Comments__c',
			'Request_City__c',
			'Status',
			'Number_in_Party__c',
			'RecordTypeId'
		],
		'01220000000hlZhAAI' => [
			'Pick_up_Date_Time__c',
			'Pick_up_Location__c',
			'Drop_Off_Date_Time__c',
			'Drop_off_Location__c',
			'Vehicule__c',
			'Transmission__c',
			'GPS__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000hlfhAAA' => [
			'Due_Date__c',
			'Category__c',
			'Request__c',
			'Comments__c',
			'Status',
			'Number_in_Party__c',
			'RecordTypeId'
		],
		'01220000000hi0YAAQ' => [
			'Category__c',
			'Request__c',
			'Comments__c',
			'Request_City__c',
			'Due_Date__c',
			'RecordTypeId'
		],
		'01220000000GK9WAAW' => [
			'Category__c',
			'Request__c',
			'Date_of_Event__c',
			'City_of_Event__c',
			'Event_Venue__c',
			'Number_in_Party__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000GK9RAAW' => [
			'Category__c',
			'Request__c',
			'Date_of_Event__c',
			'Event__c',
			'City_of_Event__c',
			'Number_in_Party__c',
			'Ticket_Category__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000GK9MAAW' => [
			'Category__c',
			'Request__c',
			'Comments__c',
			'Request_City__c',
			'Status',
			'Number_in_Party__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000hldbAAA' => [
			'AT_Arrival_Pick_Up_Date_and_Time__c',
			'AT_Arrival_Pick_Up_Location__c',
			'AT_Arrival_Flight__c',
			'AT_Arrival_Terminal__c',
			'AT_Arrival_Drop_Off_Location__c',
			'AT_Departure_Pick_Up_Date_and_Time__c',
			'AT_Departure_Pick_Up_Location__c',
			'AT_Departure_Flight__c',
			'AT_Departure_Terminal__c',
			'AT_Departure_Drop_Off_Location__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000hdCNAAY' => [
			'Departure_City__c',
			'Arrival_City__c',
			'Date_of_Departure__c',
			'Date_of_Return__c',
			'Cabin_Class__c',
			'Number_in_Party__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000hdKGAAY' => [
			'Category__c',
			'Request__c',
			'Request_City__c',
			'Status',
			'Breakfast__c',
			'Room_Category__c',
			'Check_in_Date_Time__c',
			'Check_out_Date_Time__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000jIsNAAU' => [
			'RT_Pick_Up_Date_Time__c',
			'RT_Pick_up_Location__c',
			'Drop_Off_Date_Time__c',
			'RT_Drop_Off_Location__c',
			'Date_of_Return__c',
			'RT_Vehicule__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000irh9AAA' => [
			'Request__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000hlxaAAA' => [
			'Pick_up_Date_Time__c',
			'Pick_up_Location__c',
			'Drop_Off_Date_Time__c',
			'Drop_off_Location__c',
			'Vehicule__c',
			'Comments__c',
			'RecordTypeId'
		],
		'01220000000ilsqAAA' => [
			'Subject',
			'Description'
		],
	);

	function single_request_response($request) {
		$response = json_decode(json_encode($request), True);

		$values = array();

		if(isset($this->record_type_mapping_fields[$response['RecordTypeId']])) {
			$values_to_extract = $this->record_type_mapping_fields[$response['RecordTypeId']];

			$request_id = SalesRequests::where('request_id', '=', $response['Id'])->first();

			$values['record_type'] = $request_id->record_request_mapping_id;
			$values['request_id'] = $response['Id'];

			foreach ( $values_to_extract as $value ) {
				$values[$value] = $response[$value];
			}
		}

		return $values;
	}

	function all_request_response($all_requests) {
		if( count($all_requests) > 0) {
			$requests = array();

			foreach ( $all_requests as $request ) {
				$date = new \DateTime($request->CreatedDate);

				$record_type = RecordTypeMapping::where("record_type_id", "=", $request->RecordTypeId)->first();

				if($record_type) {
					if ($record_type->belongs_to != 0) {
						$record_type_id = $record_type->belongs_to;
					} else {
						$record_type_id = $record_type->id;
					}
				} else {
					$new_record_type = new RecordTypeMapping();
					$new_record_type->record_type_id = $request->RecordTypeId;

					$new_record_type->belongs_to = 0;

					//Bitar: Check this issue.
					$record_type_id = "";
				}

				$r['id']        = $request->Id;
				$r['category']  = $request->Category__c;
				$r['title']     = $request->Subject;
				$r['date']      = intval($date->format("U"));
				$r['action']    = $request->Action__c;
				$r['type']      = $request->RecordTypeId;
				$r['status']    = $request->Status;
				$r['notification'] = 0;

				if($request->Payment_Status__c == "Paid") {
					$r['paid'] = true;
				} else {
					$r['paid'] = false;
				}
				$r['payment_due'] = $request->Member_Balance_Due__c;

				$due_date = new \DateTime($request->Due_Date__c);
				$now = new \DateTime();

				if($due_date > $now) {
					$r['is_upcoming'] = true;
				} else {
					$r['is_upcoming'] = false;
				}

				array_push($requests, $r);
			}

			return $requests;
		} else {
			return array();
		}
	}

	function request_response($all_requests) {
		if( count($all_requests) > 0) {
			$requests = array();

			//Bitar: Add Notification count to each.
			foreach($all_requests as $request) {
				$date = new \DateTime($request->CreatedDate);

				$record_type = RecordTypeMapping::where("record_type_id", "=", $request->RecordTypeId)->first();

				if ($record_type->belongs_to != 0) {
					$record_type_id = $record_type->belongs_to;
				} else {
					$record_type_id = $record_type->id;
				}

				$r['id']        = $request->Id;
				$r['category']  = $request->Category__c;
				$r['title']     = $request->Subject;
				$r['date']      = intval($date->format("U"));
				$r['action']    = $request->Action__c;
				$r['type']      = $request->RecordTypeId;
				$r['status']    = $request->Status;
				$r['notification'] = 0;

				if($request->Payment_Status__c == "Paid") {
					$r['paid'] = true;
				} else {
					$r['paid'] = false;
				}
				$r['payment_due'] = $request->Member_Balance_Due__c;

				$due_date = new \DateTime($request->Due_Date__c);
				$now = new \DateTime();

				if($due_date > $now) {
					$r['is_upcoming'] = true;
				} else {
					$r['is_upcoming'] = false;
				}

				array_push($requests, $r);
			}

			$all_statuses = StatusMapping::all();

			$statuses = array();
			foreach($all_statuses as $status) {
				array_push($statuses, $status->title);
			}

			$all_experience = RecordTypeMapping::where("belongs_to", "=", 0)->get();

			$experiences = array();
			foreach($all_experience as $experience) {
				array_push($experiences, $experience->record_type_name);
			}

			$return_data['requests'] = $requests;
			$return_data['filters']['status'] = $statuses;
			$return_data['filters']['experience'] = $experiences;

			return $return_data;
		} else {
			return array();
		}
	}


	public function membership_profile($account_profile) {

		$membership_type    = $account_profile->Membershipp__pc;
		$terms_of_payment   = $account_profile->Terms_of_Payment__c;
		$payment_method     = $account_profile->Method_of_Payment__c;

		$return_data['membership_type']     = $membership_type;
		$return_data['terms_of_payment']    = $terms_of_payment;
		$return_data['payment_method']      = $payment_method;

		$next_payment = null;

		$expiry_date_original  = explode('-', $account_profile->Membership_End_Date__pc);
		$start_date_original = explode('-', $account_profile->Membership_Start_Date__pc);

		if(count($expiry_date_original) == 0 || !isset($expiry_date_original[0]) || $expiry_date_original[0] == '') {
			$next_payment = self::get_unpaid_membership();
		} else {
			$start_date = Carbon::create($start_date_original[0], $start_date_original[1], $start_date_original[2]);
			$expiry_date = Carbon::create($expiry_date_original[0], $expiry_date_original[1], $expiry_date_original[2]);

			if(isset($terms_of_payment)) {

				$next_payment = self::get_unpaid_month($account_profile, $start_date, $expiry_date);

//				if($terms_of_payment == 'Monthly') {
//					$next_payment = self::get_unpaid_month($account_profile, $start_date, $expiry_date);
//				} elseif ($terms_of_payment == 'Annually') {
//					$next_payment = self::get_unpaid_annual($account_profile, $expiry_date);
//				} elseif ($terms_of_payment == 'Semi-Annual') {
//					$next_payment = self::get_unpaid_semi_annually($account_profile, $expiry_date);
//				}

			}
		}

		$return_data['next_payment']        = $next_payment;
		$return_data['renew_payment']       = $this->renew_payment;

		// If Notice period i.e grace period, 5 days before payment, 5 days after payment due     "Your payment is due in X days"
		// If account is suspended   "Your account is suspended"  (If payment was not made after grace period)
		// Renew Membership at the end of membership   "Don't forget to renew your membership"

//		$return_data['warning']             = 'Don\'t forget to renew your membership';
		$return_data['warning']             = '';

		return $return_data;
	}

	public function get_unpaid_month($account_profile, Carbon $start_date, Carbon $expiry_date) {


		$payment_date = null;

		$months = self::get_months($account_profile);
		$today = Carbon::now();

		$paid_months = 0;
		for($i = 0; $i < count($months); $i++) {
			if(isset($months[$i])) {
				$paid_months++;
			}
		}

		if($paid_months == 12) {
			// User has paid all his subscription fees for the entire year
			if($today->gt($expiry_date)) {
				// If user has finished his subscription and has passed the expiry date of the prior subscription we tell him to renew
				$payment_text   = 'Renew your subscription';
				$payment_date   = 0;
				$amount         = '0USD';
				$raw_amount     = 0;

				$this->renew_payment = true;
			} else {
				// If user has finished his subscription but is still in the last month
				$payment_text   = 'You have paid all your fees';
				$payment_date   = 0;
				$amount         = '0USD';
				$raw_amount     = 0;
			}
		} else {
			// User hasn't paid all his subscription fees
			$last_payment_deadline = $expiry_date;
			$last_payment_deadline = $last_payment_deadline->subMonth(1);
			$earlier_date = $today->lt($last_payment_deadline) ? $today : $last_payment_deadline;

			// Start Date with day 1
			$new_start_date = $start_date->copy();
			$new_start_date = $new_start_date->day(1);

			// Today with day 1
			$new_today_date = $earlier_date->copy();
			$new_today_date = $new_today_date->day(1);

			$diff_in_months = $new_today_date->diffInMonths($new_start_date);

			$unpaid_months = 0;
			for($i = 0; $i <= $diff_in_months; $i++) {
				if($i == $diff_in_months) {
					if(!isset($months[$i])) {
						if($today->day > $start_date->day) {
							$unpaid_months++;
						}
					}
				} else {
					if(!isset($months[$i])) {
						$unpaid_months++;
					}
				}
			}
			$payment_due = $start_date->addMonth($paid_months);

			if($unpaid_months == 0) {
				// User has paid his fees up until this day

				$payment_text   = 'Payment Due: ' . $payment_due->format('M d, Y');
				$payment_date   = $payment_due->timestamp;

				if($account_profile->Terms_of_Payment__c == $this->MONTHLY_PAYMENT_TEXT) {
					$amount         = $account_profile->Monthly_Fees__c . $account_profile->CurrencyIsoCode;
					$raw_amount     = $account_profile->Monthly_Fees__c;
				} elseif ($account_profile->Terms_of_Payment__c == $this->SEMI_ANNUALLY_PAYMENT_TEXT) {
					$amount         = ($account_profile->Monthly_Fees__c * 6) . $account_profile->CurrencyIsoCode;
					$raw_amount     = ($account_profile->Monthly_Fees__c * 6);
				} elseif ($account_profile->Terms_of_Payment__c == $this->ANNUAL_PAYMENT_TEXT) {
					$amount         = ($account_profile->Monthly_Fees__c * 12) . $account_profile->CurrencyIsoCode;
					$raw_amount     = ($account_profile->Monthly_Fees__c * 12);
				}

			} else {
				// User has outstanding fees
				$payment_text   = 'Last Payment Deadline: ' . $payment_due->format('M d, Y');
				$payment_date   = $payment_due->timestamp;

				if($account_profile->Terms_of_Payment__c == $this->MONTHLY_PAYMENT_TEXT) {
					$amount         = ($account_profile->Monthly_Fees__c * $unpaid_months) . $account_profile->CurrencyIsoCode;
					$raw_amount     = ($account_profile->Monthly_Fees__c * $unpaid_months);
				} elseif ($account_profile->Terms_of_Payment__c == $this->SEMI_ANNUALLY_PAYMENT_TEXT) {
					$amount         = ($account_profile->Monthly_Fees__c * 6) . $account_profile->CurrencyIsoCode;
					$raw_amount     = ($account_profile->Monthly_Fees__c * 6);
				} elseif ($account_profile->Terms_of_Payment__c == $this->ANNUAL_PAYMENT_TEXT) {
					$amount         = ($account_profile->Monthly_Fees__c * 12) . $account_profile->CurrencyIsoCode;
					$raw_amount     = ($account_profile->Monthly_Fees__c * 12);
				}
			}

//			print $amount . PHP_EOL;
//			print $account_profile->Terms_of_Payment__c;
//
//			exit();

//			print "New Start Date: " . $new_start_date->format('Y-m-d') . PHP_EOL;
//			print "New Today Date: " . $new_today_date->format('Y-m-d') . PHP_EOL;
//			print "Diff in Months: " . $diff_in_months . PHP_EOL;
//			print "Unpaid Months: " . $unpaid_months . PHP_EOL;
//			print "Next Payment Date: " . $start_date->addMonth($paid_months)->format('Y-m-d');
		}

		$next_payment = array(
			'payment_text' => $payment_text,
			'payment_date' => $payment_date,
			'amount' => $amount,
			'raw_amount' => $raw_amount
		);

		return $next_payment;
	}

	public function get_unpaid_semi_annually($account_profile, Carbon $expiry_date) {

		$months = self::get_months($account_profile);

		$next_payment = null;

		if(isset($months[0]) && !isset($months[6])) {
			$next_payment = array(
				'payment_text' => '2nd Payment + VAT',
				'payment_date' => $expiry_date->subMonth(6)->timestamp,
				'amount'       => ($account_profile->Monthly_Fees__c * 6) . $account_profile->CurrencyIsoCode,
			);
		} elseif(isset($months[0]) && isset($months[6])) {
			$this->renew_payment = true;
		} else {
			$next_payment = array(
				'payment_text' => '1st Payment + VAT',
				'payment_date' => $expiry_date->subMonth(12)->timestamp,
				'amount'       => (($account_profile->Monthly_Fees__c * 6) + $account_profile->VAT_Down_Payment__c) . $account_profile->CurrencyIsoCode,
			);
		}

		return $next_payment;
	}

	public function get_unpaid_membership() {
		return array (
			'payment_text'  => '',
			'payment_date'  => '',
			'amount'        => ''
		);
	}

	public function get_unpaid_annual($account_profile, Carbon $expiry_date) {

		$months = self::get_months($account_profile);

		$next_payment = null;

		if(isset($months[0])) {
			$this->renew_payment = true;
		} else {
			$next_payment = array(
				'payment_text' => '1st Payment + VAT',
				'payment_date' => $expiry_date->subMonth(12)->timestamp,
				'amount'       => (($account_profile->Monthly_Fees__c * 12) + $account_profile->VAT_Down_Payment__c) . $account_profile->CurrencyIsoCode,
			);
		}

		return $next_payment;
	}

	public function get_months($account_profile) {
		return array (
			$account_profile->Month_1__c,
			$account_profile->Month_2__c,
			$account_profile->Month_3__c,
			$account_profile->Month_4__c,
			$account_profile->Month_5__c,
			$account_profile->Month_6__c,
			$account_profile->Month_7__c,
			$account_profile->Month_8__c,
			$account_profile->Month_9__c,
			$account_profile->Month_10__c,
			$account_profile->Month_11__c,
			$account_profile->Month_12__c,
		);
	}

	public function get_last_paid_month($months) {
		for($i = 0; $i < count($months); $i++) {
			if(!isset($months[$i])) {
				return $i+1;
			}
		}
		return 0;
	}

	/**
	 *
	 */
	protected $local_monthly = 100;
	protected $local_tva = 120;
	protected $international_monthly = 300;
	protected $international_tva = 360;
	protected $signature_monthly = 667;
	protected $signature_tva = 800;

	public function calculate_vat($payment_type) {
		if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
			return $this->local_tva;
		}

		if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
			return $this->international_tva;
		}

		if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
			return $this->signature_tva;
		}
	}

	public function calculate_cost($payment_term, $payment_type, $first_payment) {
		$amount         = 0;
		$payment_text   = "";
		$annual         = 0;
		$monthly        = 0;

		if($first_payment) {
			$this->first_payment = true;
		}

		if($payment_term == $this->MONTHLY_PAYMENT) { //Monthly
			if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
				$monthly_fee    = $this->local_monthly;
				$total_vat      = $this->local_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = $monthly_fee + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee;
				}

				$annual = $monthly_fee * 12;
			}

			if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
				$monthly_fee    = $this->international_monthly;
				$total_vat      = $this->international_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = $monthly_fee + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee;
				}

				$annual = $monthly_fee * 12;
			}

			if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
				$monthly_fee    = $this->signature_monthly;
				$total_vat      = $this->signature_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = $monthly_fee + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee;
				}

				$annual = $monthly_fee * 12;
			}
		}

		if($payment_term == $this->SEMI_ANNUALLY_PAYMENT) { //Semi-annually
			if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
				$monthly_fee    = $this->local_monthly;
				$total_vat      = $this->local_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = ($monthly_fee * 6) + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee * 6;
				}

				$annual = $monthly_fee * 12;
			}

			if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
				$monthly_fee    = $this->international_monthly;
				$total_vat      = $this->international_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = ($monthly_fee * 6) + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee * 6;
				}

				$annual = $monthly_fee * 12;
			}

			if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
				$monthly_fee    = $this->signature_monthly;
				$total_vat      = $this->signature_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = ($monthly_fee * 6) + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee * 6;
				}

				$annual = $monthly_fee * 12;
			}
		}

		if($payment_term == $this->ANNUAL_PAYMENT) { //Annually
			if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
				$monthly_fee    = $this->local_monthly;
				$total_vat      = $this->local_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = ($monthly_fee * 12) + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee * 12;
				}

				$annual = $monthly_fee * 12;
			}

			if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
				$monthly_fee    = $this->international_monthly;
				$total_vat      = $this->international_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = ($monthly_fee * 12) + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee * 12;
				}

				$annual = $monthly_fee * 12;
			}

			if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
				$monthly_fee    = $this->signature_monthly;
				$total_vat      = $this->signature_tva;
				if($this->first_payment) {
					$payment_text = '1st Payment +VAT';
					$amount = ($monthly_fee * 12) + $total_vat;
				} else {
					$payment_text = 'Payment';
					$amount = $monthly_fee * 12;
				}

				$annual = $monthly_fee * 12;
			}
		}

		return array(
			'payment_text'  => $payment_text,
			'amount'        => $amount,
			'annual_fees'   => $annual,
			'monthly'       => $monthly_fee
		);
	}

	public function calculate_renew_cost($payment_term, $payment_type) {
		$amount = 0;
		$payment_text = 0;

		if($payment_term == $this->MONTHLY_PAYMENT) { //Monthly
			if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
				$monthly_fee    = $this->local_monthly;
				$total_vat      = $this->local_tva;

				$payment_text = '1st Payment +VAT';
				$amount = $monthly_fee + $total_vat;
			}

			if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
				$monthly_fee    = $this->international_monthly;
				$total_vat      = $this->international_tva;
				$payment_text = '1st Payment +VAT';
				$amount = $monthly_fee + $total_vat;
			}

			if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
				$monthly_fee    = $this->signature_monthly;
				$total_vat      = $this->signature_tva;
				$payment_text = '1st Payment +VAT';
				$amount = $monthly_fee + $total_vat;
			}
		}

		if($payment_term == $this->SEMI_ANNUALLY_PAYMENT) { //Semi-annually
			if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
				$monthly_fee    = $this->local_monthly;
				$total_vat      = $this->local_tva;
				$payment_text = '1st Payment +VAT';
				$amount = ($monthly_fee*6) + $total_vat;
			}

			if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
				$monthly_fee    = $this->international_monthly;
				$total_vat      = $this->international_tva;
				$payment_text = '1st Payment +VAT';
				$amount = ($monthly_fee*6) + $total_vat;
			}

			if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
				$monthly_fee    = $this->signature_monthly;
				$total_vat      = $this->signature_tva;
				$payment_text = '1st Payment +VAT';
				$amount = ($monthly_fee*6) + $total_vat;
			}
		}

		if($payment_term == $this->ANNUAL_PAYMENT) { //Annually
			if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
				$monthly_fee    = $this->local_monthly;
				$total_vat      = $this->local_tva;
				$payment_text = '1st Payment +VAT';
				$amount = ($monthly_fee*12) + $total_vat;
			}

			if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
				$monthly_fee    = $this->international_monthly;
				$total_vat      = $this->international_tva;
				$payment_text = '1st Payment +VAT';
				$amount = ($monthly_fee*12) + $total_vat;
			}

			if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
				$monthly_fee    = $this->signature_monthly;
				$total_vat      = $this->signature_tva;
				$payment_text = '1st Payment +VAT';
				$amount = ($monthly_fee*12) + $total_vat;
			}
		}

		return array(
			'payment_text'  => $payment_text,
			'amount'        => $amount
		);
	}

	public function build_account_profile($account_profile, $payment_term, $payment_type, $first_payment) {
//		print $payment_term . PHP_EOL;
//		print $payment_type . PHP_EOL;

		$months = self::get_months($account_profile);

		if($first_payment) {
			if($payment_term == $this->MONTHLY_PAYMENT) { //Monthly
				if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
					$account_data = array (
						'Month_1__c'                => $this->local_monthly
					);
				}

				if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
					$account_data = array (
						'Month_1__c'                => $this->international_monthly
					);;
				}

				if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
					$account_data = array (
						'Month_1__c'                => $this->signature_monthly
					);
				}
			}

			if($payment_term == $this->SEMI_ANNUALLY_PAYMENT) { //Semi-annually
				if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
					$account_data = array (
						'Month_1__c'                => $this->local_monthly,
						'Month_2__c'                => $this->local_monthly,
						'Month_3__c'                => $this->local_monthly,
						'Month_4__c'                => $this->local_monthly,
						'Month_5__c'                => $this->local_monthly,
						'Month_6__c'                => $this->local_monthly
					);
				}

				if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
					$account_data = array (
						'Month_1__c'                => $this->international_monthly,
						'Month_2__c'                => $this->international_monthly,
						'Month_3__c'                => $this->international_monthly,
						'Month_4__c'                => $this->international_monthly,
						'Month_5__c'                => $this->international_monthly,
						'Month_6__c'                => $this->international_monthly
					);
				}

				if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
					$account_data = array (
						'Month_1__c'                => $this->signature_monthly,
						'Month_2__c'                => $this->signature_monthly,
						'Month_3__c'                => $this->signature_monthly,
						'Month_4__c'                => $this->signature_monthly,
						'Month_5__c'                => $this->signature_monthly,
						'Month_6__c'                => $this->signature_monthly
					);
				}
			}

			if($payment_term == $this->ANNUAL_PAYMENT) { //Annually
				if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
					$account_data = array (
						'Month_1__c'                => $this->local_monthly,
						'Month_2__c'                => $this->local_monthly,
						'Month_3__c'                => $this->local_monthly,
						'Month_4__c'                => $this->local_monthly,
						'Month_5__c'                => $this->local_monthly,
						'Month_6__c'                => $this->local_monthly,
						'Month_7__c'                => $this->local_monthly,
						'Month_8__c'                => $this->local_monthly,
						'Month_9__c'                => $this->local_monthly,
						'Month_10__c'               => $this->local_monthly,
						'Month_11__c'               => $this->local_monthly,
						'Month_12__c'               => $this->local_monthly
					);
				}

				if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
					$account_data = array (
						'Month_1__c'                => $this->international_monthly,
						'Month_2__c'                => $this->international_monthly,
						'Month_3__c'                => $this->international_monthly,
						'Month_4__c'                => $this->international_monthly,
						'Month_5__c'                => $this->international_monthly,
						'Month_6__c'                => $this->international_monthly,
						'Month_7__c'                => $this->international_monthly,
						'Month_8__c'                => $this->international_monthly,
						'Month_9__c'                => $this->international_monthly,
						'Month_10__c'               => $this->international_monthly,
						'Month_11__c'               => $this->international_monthly,
						'Month_12__c'               => $this->international_monthly
					);
				}

				if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
					$account_data = array (
						'Month_1__c'                => $this->signature_monthly,
						'Month_2__c'                => $this->signature_monthly,
						'Month_3__c'                => $this->signature_monthly,
						'Month_4__c'                => $this->signature_monthly,
						'Month_5__c'                => $this->signature_monthly,
						'Month_6__c'                => $this->signature_monthly,
						'Month_7__c'                => $this->signature_monthly,
						'Month_8__c'                => $this->signature_monthly,
						'Month_9__c'                => $this->signature_monthly,
						'Month_10__c'               => $this->signature_monthly,
						'Month_11__c'               => $this->signature_monthly,
						'Month_12__c'               => $this->signature_monthly
					);
				}
			}
		} else {
			if($payment_term == $this->MONTHLY_PAYMENT) { //Monthly

				for($i = 0; $i < count($months); $i++) {
					if(!isset($months[$i])) {
						break;
					}
				}

				if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
					$account_data = array (
						'Month_'. ($i + 1) .'__c'                => $this->local_monthly
					);
				}

				if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
					$account_data = array (
						'Month_'. ($i + 1) .'__c'                => $this->international_monthly
					);;
				}

				if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
					$account_data = array (
						'Month_'. ($i + 1) .'__c'                => $this->signature_monthly
					);
				}
			}

			if($payment_term == $this->SEMI_ANNUALLY_PAYMENT) { //Semi-annually
				if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
					$account_data = array (
						'Month_7__c'                => $this->local_monthly,
						'Month_8__c'                => $this->local_monthly,
						'Month_9__c'                => $this->local_monthly,
						'Month_10__c'               => $this->local_monthly,
						'Month_11__c'               => $this->local_monthly,
						'Month_12__c'               => $this->local_monthly
					);
				}

				if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
					$account_data = array (
						'Month_7__c'                => $this->international_monthly,
						'Month_8__c'                => $this->international_monthly,
						'Month_9__c'                => $this->international_monthly,
						'Month_10__c'                => $this->international_monthly,
						'Month_11__c'                => $this->international_monthly,
						'Month_12__c'                => $this->international_monthly
					);
				}

				if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
					$account_data = array (
						'Month_7__c'                => $this->signature_monthly,
						'Month_8__c'                => $this->signature_monthly,
						'Month_9__c'                => $this->signature_monthly,
						'Month_10__c'                => $this->signature_monthly,
						'Month_11__c'                => $this->signature_monthly,
						'Month_12__c'                => $this->signature_monthly
					);
				}
			}

			if($payment_term == $this->ANNUAL_PAYMENT) { //Annually
				if($payment_type == $this->LOCAL_MEMBERSHIP) { //Local
					$account_data = array (
						'Month_1__c'                => $this->local_monthly,
						'Month_2__c'                => $this->local_monthly,
						'Month_3__c'                => $this->local_monthly,
						'Month_4__c'                => $this->local_monthly,
						'Month_5__c'                => $this->local_monthly,
						'Month_6__c'                => $this->local_monthly,
						'Month_7__c'                => $this->local_monthly,
						'Month_8__c'                => $this->local_monthly,
						'Month_9__c'                => $this->local_monthly,
						'Month_10__c'               => $this->local_monthly,
						'Month_11__c'               => $this->local_monthly,
						'Month_12__c'               => $this->local_monthly
					);
				}

				if($payment_type == $this->INTERNATIONAL_MEMBERSHIP) { //International
					$account_data = array (
						'Month_1__c'                => $this->international_monthly,
						'Month_2__c'                => $this->international_monthly,
						'Month_3__c'                => $this->international_monthly,
						'Month_4__c'                => $this->international_monthly,
						'Month_5__c'                => $this->international_monthly,
						'Month_6__c'                => $this->international_monthly,
						'Month_7__c'                => $this->international_monthly,
						'Month_8__c'                => $this->international_monthly,
						'Month_9__c'                => $this->international_monthly,
						'Month_10__c'               => $this->international_monthly,
						'Month_11__c'               => $this->international_monthly,
						'Month_12__c'               => $this->international_monthly
					);
				}

				if($payment_type == $this->SIGNATURE_MEMBERSHIP) { //Signature
					$account_data = array (
						'Month_1__c'                => $this->signature_monthly,
						'Month_2__c'                => $this->signature_monthly,
						'Month_3__c'                => $this->signature_monthly,
						'Month_4__c'                => $this->signature_monthly,
						'Month_5__c'                => $this->signature_monthly,
						'Month_6__c'                => $this->signature_monthly,
						'Month_7__c'                => $this->signature_monthly,
						'Month_8__c'                => $this->signature_monthly,
						'Month_9__c'                => $this->signature_monthly,
						'Month_10__c'               => $this->signature_monthly,
						'Month_11__c'               => $this->signature_monthly,
						'Month_12__c'               => $this->signature_monthly
					);
				}
			}
		}

		return $account_data;
	}

	public function get_membership_type_by_text($type) {
		if($type == 'Local') {
			return $this->LOCAL_MEMBERSHIP;
		} elseif($type == 'International') {
			return $this->INTERNATIONAL_MEMBERSHIP;
		} elseif($type == 'Signature') {
			return $this->SIGNATURE_MEMBERSHIP;
		}
	}

	public function get_payment_terms_by_text($type) {
		if($type == 'Monthly') {
			return $this->MONTHLY_PAYMENT;
		} elseif($type == 'Annually') {
			return $this->ANNUAL_PAYMENT;
		} elseif($type == 'Semi-Annaul') {
			return $this->SEMI_ANNUALLY_PAYMENT;
		}
	}
}
<?php

namespace App\Helpers;


use App\Models\Api\RecordRequestMapping;
use App\Models\Api\RecordTypeMapping;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JsonBuilder {

	public $mapping_array = [];

	public static function concierge_general_request(Request $request, $sales_force_id) {
		$city           = $request->get('city');
		$date_time      = $request->get('date_time');
		$details        = $request->get('details');
		$record_type    = $request->get('record_type');
		$passengers_num = $request->get('passengers_num');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$return_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_time);

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Due_Date__c'           => $return_date->format('Y-m-d'),
			'Category__c'           => $record_type_mapping_id->category,
			'Request__c'            => $record_type_mapping_id->request,
			'Comments__c'           => $details,
			'Request_City__c'       => $city,
			'Status'                => 'New',
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		if(isset($passengers_num) && $passengers_num != '') {
			$request_array['Number_in_Party__c'] = $passengers_num;
		}

		return $request_array;
	}

	public static function concierge_reservation_request(Request $request, $sales_force_id) {
		$date_time      = $request->get('date_time');
		$comment        = $request->get('comment');
		$record_type    = $request->get('record_type');
		$passengers_num = $request->get('passengers_num');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$return_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_time);

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Due_Date__c'           => $return_date->format('Y-m-d'),
			'Category__c'           => $record_type_mapping_id->category,
			'Request__c'            => $record_type_mapping_id->request,
			'Comments__c'           => $comment,
			'Status'                => 'New',
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name,
			'Number_in_Party__c'    => $passengers_num
		);

		return $request_array;
	}

	public static function concierge_shopping_request(Request $request, $sales_force_id) {
		$comment        = $request->get('comment');
		$record_type    = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Category__c'           => $record_type_mapping_id->category,
			'Request__c'            => $record_type_mapping_id->request,
			'Comments__c'           => $comment,
			'Status'                => 'New',
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name,
		);

		return $request_array;
	}

	public static function concierge_rent_request(Request $request, $sales_force_id) {
		$pick_up_date_time  = $request->get('pick_up_date_time');
		$pick_up_location   = $request->get('pick_up_location');
		$drop_off_date_time = $request->get('drop_off_date_time');
		$drop_off_location  = $request->get('drop_off_location');
		$vehicle            = $request->get('vehicle');
		$transmission       = $request->get('transmission');
		$gps                = $request->get('gps');
		$record_type        = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$pick_up_date_time      = Carbon::createFromFormat('Y-m-d H:i:s', $pick_up_date_time);
		$drop_off_date_time     = Carbon::createFromFormat('Y-m-d H:i:s', $drop_off_date_time);

		$request_array = array (
			'RecordTypeId'              => $record_type_id->record_type_id,
			'Pick_up_Date_Time__c'      => $pick_up_date_time->toIso8601String(),
			'Pick_up_Location__c'       => $pick_up_location,
			'Drop_Off_Date_Time__c'     => $drop_off_date_time->toIso8601String(),
			'Drop_off_Location__c'      => $drop_off_location,
			'Vehicule__c'               => $vehicle,
			'Transmission__c'           => $transmission,
			'GPS__c'                    => $gps,
			'AccountId'                 => $sales_force_id,
			'Subject'                   => 'Car Rental ' . $vehicle
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		return $request_array;
	}

	public static function concierge_transfer_request(Request $request, $sales_force_id) {
		$pick_up_date_time  = $request->get('pick_up_date_time');
		$pick_up_location   = $request->get('pick_up_location');
		$drop_off_date_time = $request->get('drop_off_date_time');
		$drop_off_location  = $request->get('drop_off_location');
		$vehicle            = $request->get('vehicle');
		$record_type        = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$pick_up_date_time      = Carbon::createFromFormat('Y-m-d H:i:s', $pick_up_date_time);
		$drop_off_date_time     = Carbon::createFromFormat('Y-m-d H:i:s', $drop_off_date_time);

		$request_array = array (
			'RecordTypeId'              => $record_type_id->record_type_id,
			'Pick_up_Date_Time__c'      => $pick_up_date_time->toIso8601String(),
			'Pick_up_Location__c'       => $pick_up_location,
			'Drop_Off_Date_Time__c'     => $drop_off_date_time->toIso8601String(),
			'Drop_off_Location__c'      => $drop_off_location,
			'Vehicule__c'               => $vehicle,
			'AccountId'                 => $sales_force_id,
			'Subject'                   => 'Concierge Transfer ' . $vehicle
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		return $request_array;
	}

	public static function concierge_round_trip_request(Request $request, $sales_force_id) {
		$pick_up_date_time  = $request->get('pick_up_date_time');
		$pick_up_location   = $request->get('pick_up_location');
		$drop_off_date_time = $request->get('drop_off_date_time');
		$drop_off_location  = $request->get('drop_off_location');
		$return_date_time   = $request->get('return_date_time');
		$vehicle            = $request->get('vehicle');
		$record_type        = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();


		$pick_up_date_time      = Carbon::createFromFormat('Y-m-d H:i:s', $pick_up_date_time);
		$drop_off_date_time     = Carbon::createFromFormat('Y-m-d H:i:s', $drop_off_date_time);
		$return_date_time       = Carbon::createFromFormat('Y-m-d H:i:s', $return_date_time);

		$request_array = array (
			'RecordTypeId'              => $record_type_id->record_type_id,
			'RT_Pick_Up_Date_Time__c'   => $pick_up_date_time->toIso8601String(),
			'RT_Pick_up_Location__c'    => $pick_up_location,
			'Drop_Off_Date_Time__c'     => $drop_off_date_time->toIso8601String(),
			'RT_Drop_Off_Location__c'   => $drop_off_location,
			'Date_of_Return__c'         => $return_date_time->toIso8601String(),
			'RT_Vehicule__c'            => $vehicle,
			'AccountId'                 => $sales_force_id,
			'Subject'                   => 'Round Trip Transfer ' . $vehicle
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		return $request_array;
	}



	public static function event_general_request(Request $request, $sales_force_id) {
		$city           = $request->get('city');
		$event_date     = $request->get('event_date');
		$venue          = $request->get('venue');
		$party_num      = $request->get('party_num');
		$record_type    = $request->get('record_type');
		$comment        = $request->get('comment');
		$category       = $request->get('category');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$event_date    = Carbon::createFromFormat('Y-m-d H:i:s', $event_date);

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Category__c'           => $category,
			'Request__c'            => $record_type_mapping_id->request,
			'Date_of_Event__c'      => $event_date->toIso8601String(),
			'City_of_Event__c'      => $city,
			'Event_Venue__c'        => $venue,
			'Number_in_Party__c'    => $party_num,
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		return $request_array;
	}



	public static function ticket_general_request(Request $request, $sales_force_id) {
		$ticket_date    = $request->get('ticket_date');
		$event          = $request->get('event');
		$location       = $request->get('location');
		$party_num      = $request->get('party_num');
		$category       = $request->get('category');
		$sport_type     = $request->get('sport_type');
		$record_type    = $request->get('record_type');
		$comment        = $request->get('comment');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$ticket_date    = Carbon::createFromFormat('Y-m-d H:i:s', $ticket_date);

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Category__c'           => $record_type_mapping_id->category,
			'Request__c'            => $record_type_mapping_id->request,
			'Date_of_Event__c'      => $ticket_date->toIso8601String(),
			'Event__c'              => $event,
			'City_of_Event__c'      => $location,
			'Number_in_Party__c'    => $party_num,
			'Ticket_Category__c'    => $category,
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		if(isset($sport_type) && $sport_type != '') {
			$request_array['Ticket_Type__c'] = $sport_type;
		}

		return $request_array;
	}



	public static function travel_general_means(Request $request, $sales_force_id) {
		$city           = $request->get('city');
		$details        = $request->get('details');
		$passengers_num = $request->get('passengers_num');
		$record_type    = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Category__c'           => $record_type_mapping_id->category,
			'Request__c'            => $record_type_mapping_id->request,
			'Comments__c'           => $details,
			'Request_City__c'       => $city,
			'Status'                => 'New',
			'AccountId'             => $sales_force_id,
			'Number_in_Party__c'    => $passengers_num,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		return $request_array;
	}

	public static function travel_hotel(Request $request, $sales_force_id) {
		$city           = $request->get('city');
		$hotel_name     = $request->get('hotel_name');
		$check_in_date  = Carbon::createFromFormat('Y-m-d H:i:s', $request->get('check_in_date'));
		$nights         = $request->get('nights');
		$room_type      = $request->get('room_type');
		$breakfast      = $request->get('breakfast'); //Included //Not Included
		$record_type    = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$check_out_date = $check_in_date->addDays($nights);

		$request_array = array (
			'RecordTypeId'              => $record_type_id->record_type_id,
			'Category__c'               => $record_type_mapping_id->category,
			'Request__c'                => $record_type_mapping_id->request,
			'Request_City__c'           => $city,
			'Status'                    => 'New',
			'Breakfast__c'              => $breakfast,
			'Room_Category__c'          => $room_type,
			'Check_in_Date_Time__c'     => $check_in_date->toIso8601String(),
			'Check_out_Date_Time__c'    => $check_out_date->toIso8601String(),
			'AccountId'                 => $sales_force_id,
			'Subject'                   => $record_type_id->record_type_name,
			'Description'               => $record_type_id->record_type_name,
			'Comments__c'               => $hotel_name
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $hotel_name . ' ' . $comment;
		}

		return $request_array;
	}

	public static function travel_train_request(Request $request, $sales_force_id) {
		$comment        = $request->get('comment');
		$record_type    = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Request__c'            => $record_type_mapping_id->request,
			'Comments__c'           => $comment,
			'Status'                => 'New',
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		return $request_array;
	}

	public static function travel_flight_request(Request $request, $sales_force_id) {
		$flying_from    = $request->get('flying_from');
		$flying_to      = $request->get('flying_to');
		$departure_date = $request->get('departure_date');
		$returning_date = $request->get('returning_date');
		$passengers_num = $request->get('passengers_num');
		$class          = $request->get('class');
		$comment        = $request->get('comment');
		$record_type    = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$depart_date = Carbon::createFromFormat('Y-m-d H:i:s', $departure_date);
		$return_date = Carbon::createFromFormat('Y-m-d H:i:s', $returning_date);

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Departure_City__c'     => $flying_from,
			'Arrival_City__c'       => $flying_to,
			'Date_of_Departure__c'  => $depart_date->toIso8601String(),
			'Date_of_Return__c'     => $return_date->toIso8601String(),
			'Number_in_Party__c'    => $passengers_num,
			'Cabin_Class__c'        => $class,
			'AccountId'             => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		return $request_array;
	}

	public static function travel_airport_transfer_request(Request $request, $sales_force_id) {
		$arrival_date               = $request->get('arrival_date');
		$arrival_airport            = $request->get('arrival_airport');
		$arrival_flight_number      = $request->get('arrival_flight_number');
		$arrival_terminal           = $request->get('arrival_terminal');
		$arrival_destination        = $request->get('arrival_destination');

		$departure_date             = $request->get('departure_date');
		$departure_airport          = $request->get('departure_airport');
		$departure_flight_number    = $request->get('departure_flight_number');
		$departure_terminal         = $request->get('departure_terminal');
		$departure_destination      = $request->get('departure_destination');

		$comment                    = $request->get('comment');

		$record_type    = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();
		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$arrival_date   = Carbon::createFromFormat('Y-m-d H:i:s', $arrival_date);
		$departure_date = Carbon::createFromFormat('Y-m-d H:i:s', $departure_date);

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'AT_Arrival_Pick_Up_Date_and_Time__c'       => $arrival_date->toIso8601String(),
			'AT_Arrival_Pick_Up_Location__c'            => $arrival_airport,
			'AT_Arrival_Flight__c'                      => $arrival_flight_number,
			'AT_Arrival_Terminal__c'                    => $arrival_terminal,
			'AT_Arrival_Drop_Off_Location__c'           => $arrival_destination,
			'AT_Departure_Pick_Up_Date_and_Time__c'     => $departure_date->toIso8601String(),
			'AT_Departure_Pick_Up_Location__c'          => $departure_airport,
			'AT_Departure_Flight__c'                    => $departure_flight_number,
			'AT_Departure_Terminal__c'                  => $departure_terminal,
			'AT_Departure_Drop_Off_Location__c'         => $departure_destination,
			'AccountId'                                 => $sales_force_id,
			'Subject'               => $record_type_id->record_type_name,
			'Description'           => $record_type_id->record_type_name
		);

		if(isset($comment) && $comment != '') {
			$request_array['Comments__c'] = $comment;
		}

		return $request_array;
	}



	public static function other_request(Request $request, $sales_force_id) {
		$subject            = $request->get('subject');
		$description        = $request->get('description');
		$record_type        = $request->get('record_type');

		$record_type_mapping_id = RecordRequestMapping::where('record_request_mapping.id', '=', $record_type)->first();

		$record_type_id = RecordTypeMapping::where('record_type_mapping.id', '=', $record_type_mapping_id->record_type_id)->first();

		$request_array = array (
			'RecordTypeId'          => $record_type_id->record_type_id,
			'Status'                => 'New',
			'AccountId'             => $sales_force_id,
			'Subject'               => $subject,
			'Description'           => $description
		);

		return $request_array;
	}

}
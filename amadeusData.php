<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$apiKey = $_ENV['API_KEY'];
$apiSecret = $_ENV['API_SECRET'];
$baseUrl = 'https://test.api.amadeus.com';


#==========
#getAccessToken
#==========
function getAccessToken($apiKey, $apiSecret){
	$url = 'https://test.api.amadeus.com/v1/security/oauth2/token';

	$header= [
		'grant_type' => 'client_credentials',
		'client_id' => $apiKey,
		'client_secret' => $apiSecret
	];

	$ch = curl_init($url);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($header));

	$response = curl_exec($ch);
	curl_close($ch);
	$responseData = json_decode($response, true);
	return $responseData['access_token'] ?? null;
}

#==========
#insertData
#==========
function insertData($insertQuery){
// based on doLogin from testRabbitMQServer.php
	$host = 'node2';
    	$dbUser = 'test';
    	$dbPass = 'test';
    	$dbName = 'it490';

    // Create a database connection
    	$conn = mysqli_connect($host, $dbUser, $dbPass, $dbName);

    // Check for connection errors
    	if ($conn->connect_error) {
        	//return array("returnCode" => '0', 'message' => "Database connection error");
    		exit();
	}
	
	try {
		$stmt = $conn->prepare($insertQuery);
		//TODO bind_param here, have insertData accept another argument of an array? or too much?
		$stmt->execute();
	} finally {
        //close afterwards or else you're causing a DoS, no good!
        if ($stmt) {
            $stmt->close();
        }
        $conn->close();
    }
}


#==========
#flightList
#==========
function flightList($accessToken, $locationCode){
	$callUrl = (string)"/v1/shopping/flight-destinations?origin=" . $locationCode;
	
	$data = processCall($accessToken, $callUrl);
	if (!empty($data)) {
		foreach ($data as $entry){
			$origin = $entry['origin'];
			$destination = $entry['destination'];
			$departure_date = $entry['departureDate'];
	                $return_date = $entry['returnDate'];
	                $price = $entry['price']['total'];
	                $flight_dates_link = $entry['links']['flightDates'];
	                $flight_offers_link = $entry['links']['flightOffers'];
			
			//TODO if possible fix to bind params, currently doing this way for testing functionality
			$insertQuery = "INSERT INTO flights (origin_code, destination_code, departure_date, return_date, price, flight_dates_link, flight_offers_link) VALUES ('$origin', '$destination', '$departure_date', '$return_date', '$price', '$flight_dates_link', '$flight_offers_link')";
			insertData($insertQuery);
		}
	}
}


#==========
#hotelList
#==========
function hotelList($accessToken, $locationCode){
	$callUrl = (string)"/v1/reference-data/locations/hotels/by-city?cityCode=" . $locationCode;

	$data = processCall($accessToken, $callUrl);
	if (!empty($data)) {
		foreach ($data as $entry){
			$chain_code = $entry['chainCode'];
			$iata_code = $entry['iataCode'];
			$dupe_id = $entry['dupeId'];
			$name = $entry['name'];
			$hotel_id = $entry['hotelId'];
			$latitude = $entry['geoCode']['latitude'];
			$longitude = $entry['geoCode']['longitude'];
			$country_code = $entry['address']['countryCode'];
			$last_update = $entry['lastUpdate'];
			

			//TODO if possible fix to bind params, currently doing this way for testing functionality
			$insertQuery = "INSERT INTO hotels (location_code, hotel_id, hotel_name, latitude, longitude, country_code, last_update, dupe_id, chain_code) VALUES ('$iata_code', '$hotel_id', '$name', '$latitude', '$longitude', '$country_code', '$last_update', '$dupe_id', '$chain_code')";
			insertData($insertQuery);
		}
	}
}

#==========
#processCall
#==========
function processCall($accessToken, $callUrl){
	global $baseUrl;

	//Working below !!
	//
	//$url = $baseUrl . "/v1/reference-data/locations/hotels/by-city?cityCode=PAR";
	
	//this also works but needs specifications
	//$url = $baseUrl . "/v2/shopping/flight-offers";

	//this works best
	//$url = $baseUrl . "/shopping/flight-destinations?origin=PAR";


	$url = $baseUrl . $callUrl;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Bearer $accessToken",
	]);

	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response,true);
}

#==========

function requestApi($request){
	$locationCode = $request['location'];
	$accessToken = getAccessToken($apiKey, $apiSecret);
	if ($accessToken){
		switch ($request['type']){
			case 'flight':
				$results = flightList($accessToken, $locationCode);
				break;
			case 'hotel':
				$results = hotelList($accessToken, $locationCode);
	  			break;
		}
		print_r($results);
	} else {
		echo "FAILED: NO ACCESS TOKEN\n";
	}
}


?>

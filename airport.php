<?php
require './getData.php';
require './getHistory.php';
require './xml.php';
require './mysqli.php';
$getData = new GetData();
$getHistory = new GetHistory();
$xml = new XML();
$mysqli = new MysqliDataBase();
$responses = array();
$allFlights = array();
$finalFlights = array();
$allHistories = array();


$hour =  intval(date("H"));
$date = date("Ymd");

if ($hour <= 6){
    $response = $getData->curlCall('https://www.beauvais-airport.com/bva-departures?tp=0');
    array_push($responses, $response);
}
if ($hour <= 12) {
    $response = $getData->curlCall('https://www.beauvais-airport.com/bva-departures?tp=6');
    array_push($responses, $response);
}
if ($hour <= 18) {
    $response = $getData->curlCall('https://www.beauvais-airport.com/bva-departures?tp=12');
    array_push($responses, $response);
}
if ($hour <= 23) {
    $response = $getData->curlCall('https://www.beauvais-airport.com/bva-departures?tp=18');
    array_push($responses, $response);
}

if ($responses) {
    foreach ($responses as $response) {
        $data = $getData->selectInResponse($response, '/html/body/main/div/div[1]/div[6]/div[2]/div[2]');
        $formatedFlights = $getData->formatFlights($data);
        array_push($allFlights, $formatedFlights);
    }

    foreach ($allFlights as $flights) {
        foreach ($flights as $flight) {
            if($flight['Status']!= null){
                $lastFlights = $getData->curlCall('https://fr.trip.com/flights/status-'.$flight['Flight'].'/');
                $data = $getHistory->selectInResponse($lastFlights);
                foreach ($data as $history) {
                    $h = $history->nodeValue;
                    $dates = array();
                    $history = $getHistory->formatFlights($h, $dates);
                }
                $flight["history"] = json_encode($history);
                $newFlight = $flight;
                array_push($finalFlights, $newFlight);
            }
           
        }
    }
    var_dump($finalFlights);
    $xml->createXmlFile($finalFlights);
    $mysqli->createtable();
    $mysqli->addOccurence();


}
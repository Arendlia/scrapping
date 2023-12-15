<?php
class XML
{
    public function createXmlFile($tab_object){
        $dom = new DOMDocument();

		$dom->encoding = 'utf-8';

		$dom->xmlVersion = '1.0';

		$dom->formatOutput = true;

	    $xml_file_name = 'flights_list.xml';

		$root = $dom->createElement('Flights');

        foreach($tab_object as $object) {
			if($object['Flight']!= null) {
				$flight = $dom->createElement('Flight');
				$flight_id = new DOMAttr('numero', $object['Flight']);
				$flight->setAttributeNode($flight_id);
				$destination = $dom->createElement('Destination', $object['Destination']);
				$departure = $dom->createElement('Departure', $object['Departure']);
				$flightId = $dom->createElement('FlightId', $object['Flight']);
				$airline = $dom->createElement('Airline', $object['Airline']);
				$status = $dom->createElement('Status', $object['Status']);
				$history = $dom->createElement('History', $object['history']);
				$history_commentary = new DOMAttr('valeur', "En minute si la valeure est négative c'est du retard");
				$history->setAttributeNode($history_commentary);
				$flight->appendChild($destination);
				$flight->appendChild($departure);
				$flight->appendChild($flightId);
				$flight->appendChild($airline);
				$flight->appendChild($status);
				$flight->appendChild($history);
				$root->appendChild($flight);
			}
        }
		$dom->appendChild($root);

		$dom->save($xml_file_name);

		echo "$xml_file_name has been successfully created";
    }

}
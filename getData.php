<?php
class GetData
{
    public $allFlights = array();

    public function curlCall($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Erreur cURL : ' . curl_error($curl);
        }else{
            return $response;
        }
        curl_close($curl);
    }

    public function selectInResponse($response, $xpath){
        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($response);
        libxml_use_internal_errors(false);
        $data = $doc->getElementsByTagName('td');
        $xpath = new DOMXPath($doc);
        $data = $xpath->query('/html/body/main/div/div[1]/div[6]/div[2]/div');
        return $data;
    }

    public function getTagNames(){
        $col1 = "Destination";
        $col2 = "Departure";
        $col3 = "Flight";
        $col4 = "Airline";
        $col5 = "Status";
        return array($col1, $col2, $col3, $col4,$col5);

    }

    public function format($f){
        echo($f);
        $destination = explode(')', $f)[0].')';
           $departure = substr(explode(')', $f)[1],0,5);
           $flight = substr(explode(')', $f)[1],5,6);
           $compagny = explode(':', explode(')', $f)[1])[1];
           $airline = explode('Ter', $compagny);
           $search = "easyJet";
           if(preg_match("/{$search}/i", $compagny)) {
                $compagny = 'easyJet';
                $terminal = 0;
                $status = preg_split("/Terminal/", substr(explode(')', $f)[1],11))[0];
           } else{
                // if(explode(')', $f)[1]){
                // $status = preg_split("/Terminal/", substr(explode(')', $f)[1],11))[1];
                // }
                if(preg_match("/Terminal/i", $f)){
                    $state = explode('Terminal', $f);
                    $status = substr($state[1],1);
                }else{
                    $status = "Scheduled[+]";
                }

           }
           $terminals = array("0","1", "2", "3", "4", "5", "6", "7", "8", "9", "FR");
           $airline = str_replace($terminals, "", $airline[0]);
           $airline = preg_replace('/W/', "", $airline, 1);
           $status = str_replace($terminals, "", $status);

           $array = array($destination, $departure, $flight, $airline, $status);
           return $array;
    }

    public function formatFlights($flights){
        $data = array();
        $col = array();
        foreach ($flights as $flight) {
            $f = $flight->nodeValue;
            $f = preg_replace('/\s+/', '', $f);
            if($f == "DestinationDepartureFlightAirlineTerminalStatus"){
                $col = $this->getTagNames();
            }
            else{
               $data = $this->format($f);
            }
              $associatedTab = array($col[0] => $data[0], $col[1] => $data[1], $col[2] =>$data[2], $col[3] =>$data[3], $col[4]=>$data[4], 'history' => null);
              array_push($this->allFlights, $associatedTab);
        }
        return $this->allFlights;
    }

}

?>
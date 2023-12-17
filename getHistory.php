<?php
class GetHistory
{
    public $allHistory = array();
    

    public function selectInResponse($response){
        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($response);
        libxml_use_internal_errors(false);
        $data = $doc->getElementsByTagName('td');
        $xpath = new DOMXPath($doc);
        $data = $xpath->query('/html/body/div[1]/div[6]/div[8]/div/div/div/table/tbody');
        return $data;
    }

   public function formatFlights($flightHistory, $array){
        $index =0;
        $index_hours =0;
        $now = intval(date("d"));
        $totalDepart = 0;
        $totalArrivées = 0;
        $dates = array();
        $informations = array();
        $information = array();
        $totalFlightsHours = 0;
        $hours =0;
        $nbRetardedDepartures = 0;
        $annulatedFlights = 0;

        $f =  explode('.', $flightHistory);
        foreach ($f as $date) {
            if($index%3 == 0 && $index%2 == 0){
                $hours =  explode('-', $f[$index],2);
                $time =  explode('Arrivé', $f[$index]);
                $out = preg_replace('~\D~', '', $time[0]);
                $hours = str_split($out, 2);
                if(count($hours)==8){
                    foreach($hours as $h){
                        if($index_hours%2 == 0){
                            if($hours[$index_hours] == "--:--" && $hours[$index_hours+1]== "--:--"){
                                $annulatedFlights == $annulatedFlights +1;
                            }
                            if(intval($hours[$index_hours]) == 0){
                                $hours[$index_hours] =24;
                            }
                            $hour =  intval($hours[$index_hours]) *60 + intval($hours[$index_hours+1]);
                            array_push($information, $hour);
                        }
                        $index_hours = $index_hours+1;
                    }
                    $depart = $information[0]-$information[2];
                    $arrivé = $information[1]-$information[3];
                    $totalDepart = $totalDepart + $depart;
                    $totalArrivées = $totalArrivées + $arrivé;
                    $hours = $information[3] - $information[2];
                    var_dump($information);

                    $totalFlightsHours = $totalFlightsHours + $hours;
                    print($totalFlightsHours);

                    if($depart < 0){
                        $nbRetardedDepartures = $nbRetardedDepartures +1;
                    }
                }
                
            }
                

            $index = $index + 1;
        }
        
        return array("arrivals" => $totalArrivées, "departures" =>$totalDepart, "totalFlightsMinutes" => $totalFlightsHours, "nbRetardedDepartures" => $nbRetardedDepartures, "annulatedFlights" => $annulatedFlights);
        

   }

}


?>
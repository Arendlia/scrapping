<?php
class MysqliDataBase
{
    public function connect(){
        $mysqli = new mysqli('127.0.0.1','root','','airport');
        /*connecting with mysql*/
        if(!$mysqli->connect_errno){
			return $mysqli;
        echo "Connection Successful";
        } else {
            echo $mysqli->connect_error;
			return null;
        }
    }

    public function createtable(){
		$mysqli = $this->connect();
       	if (!$this->connect()){
		 return false;
	   }else {
		$sql = 'CREATE TABLE IF NOT EXISTS flights(
			id VARCHAR(10) NOT NULL,
			destination varchar(100) NOT NULL,
			departure varchar(6) NOT NULL,
			airline varchar(100) NOT NULL,
			status varchar(100) NOT NULL,
			history varchar(100),
			PRIMARY KEY (id)
			)';
			$result = $mysqli->query($sql);
			if(!$result){
				echo "Query Failed. ".$mysqli->error;
			} else {
				echo "Query Successfull";
			}
			$mysqli->close();//close the connection
		}
    }

	public function addOccurence(){
		$mysqli = $this->connect();
       	if (!$this->connect()){
		 return false;
	   }else {
		$xml = simplexml_load_file("./flights_list.xml") or die("Error: Cannot create object");
		foreach ($xml->children() as $row) {
			$id = $row->FlightId;
			$departure = $row->Departure;
			$airline = $row->Airline;
			$destination = $row->Destination;
			$status = $row->Status;
			$history = $row->History;
			
			$sql = "INSERT INTO flights(id,departure,airline,destination,status, history) 
			VALUES ('" . $id . "','" . $departure . "','" . $airline . "','" . $destination .  "','" .$status.  "','" .$history. "')
			ON DUPLICATE KEY UPDATE id='" . $id . "', departure='" . $departure . "', airline = '" . $airline . "', destination= '" . $destination .  "', status = '" .$status.  "', history = '" .$history. "'";
			$result = mysqli_query($mysqli, $sql);
		}
		$mysqli->close();//close the connection
		}
			
			
    }

	

}

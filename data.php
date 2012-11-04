<?php 

	$connection = mysql_connect("localhost:8888", "infovis2", "infovis2");
	mysql_select_db("infovis2", $connection);

	if(!$connection){
	
		echo "Connection to the database failed.";
	
	} else {


	}

	if(isset($_POST["month"])){


		$month = $_POST["month"];
		//echo $month;

	} else {

		$month = 1;
	}

	if(isset($_POST["year"])){


		$year = $_POST["year"];
		//echo $month;

	} else {

		$year = 2009;
	}

	if(isset($_POST["weekday"])){

		$weekday = $_POST["weekday"];
		$weekdayQuery = "AND weekday = ". $weekday . "\"";

	file_put_contents($file, $current);
	} else {

		$weekday = Null;
		$weekdayQuery = "";
	}


	//$sqlString = "SELECT username, COUNT(*) as convoCount FROM conversations WHERE year = 2008 AND username != 'Me' GROUP BY username";
	//echo $sqlString;

	//$query = mysql_query($sqlString) or die(mysql_errno($connection);

	//working other
	//$query = mysql_query("SELECT username, COUNT(*) as convoCount FROM conversations WHERE year = 2008 AND month = ". $month . " AND username != 'Me' GROUP BY username") or die(mysql_errno($connection));
	//************ Start Working ************



	// Database Queries
	
	$query = mysql_query("SELECT username, COUNT(*) as convoCount FROM conversations WHERE year = ". $year ." AND month = ". $month . "" . $weekdayQuery . " AND username != 'Me' AND username != 'aolsystemmsg' GROUP BY username") or die(mysql_errno($connection));
		
	//************ End Working ************


	$rows = array();

	while($row = mysql_fetch_assoc($query)) {

		$rows[] = $row;
	}
	//echo json_encode($rows);
	echo json_encode($rows);

?>
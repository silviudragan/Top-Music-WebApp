<?php

	function connect(){
		//Oracle DB user name
		$username = 'PROJECT';

		// Oracle DB user password
		$password = 'PROJECT';

		// Oracle DB connection string
		$connection_string = 'localhost/xe';

		//Connect to an Oracle database
		$connection = oci_connect(
		$username,
		$password,
		$connection_string
		);

		If (!$connection)
		echo 'Connection failed';
		else
		echo 'Connected to Oracle DB';

		return $connection;
	}	
?>


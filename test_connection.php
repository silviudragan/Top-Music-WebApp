<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Welcome page</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/home-page.css" rel="stylesheet">

</head>

<body style="background-color:#ebebe0;">

	<div class="navbar-collapse collapse" style="background-color:#d6d6c2; margin-top: 0px;">
		<ul class="nav navbar-nav navbar-left" style="margin-left: 50px;">
			<li><a href="test_connection.php"> Home </a></li>
			<li><a href="login.php">Login</a></li>
			<li><a href="register.php">Register</a></li>
		</ul>
	</div>




	<?php

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

	?>

	<h3 style="text-align: center;">Top 10 music voted</h3>
	<?php
		// Prepare the statement
		$stid = oci_parse($connection, 'SELECT * from (SELECT rank()over(order by votes desc) as pozitie, id_song, name, link, votes, posted_time from SONGS) where pozitie <= 10');
		if (!$stid) {
		    $e = oci_error($connection);
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}

		// Perform the logic of the query
		$r = oci_execute($stid);
		if (!$r) {
		    $e = oci_error($stid);
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}

		// Fetch the results of the query
		print "<table border='1' align='center'>\n";
		print "<tr>\n";
		print "<td>Rank</td><td> ID Song </td><td> Song </td><td> Link </td><td> Votes </td><td> Published Date </td>";
		print "</tr>";
		while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		    print "<tr>\n";
		    foreach ($row as $item) {
		        print "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
		    }
		    print "</tr>\n";
		}
		print "</table>\n";

		oci_free_statement($stid);
		// Close connection 
		//oci_close($connection);

	?>
	</body>


</html>
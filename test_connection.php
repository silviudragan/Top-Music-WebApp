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

		require_once(dirname(__FILE__) . '/functions.php');
		$connection = connect();

	?>

	<h3 style="text-align: center;">Top 1000 music voted</h3>
	<?php

		//Number of rows
			$rows_per_page = 100;
			$stid = oci_parse($connection, "SELECT count(*) from (SELECT rank()over(order by votes desc) as pozitie, id_song, name, link, votes, posted_time from SONGS) where pozitie <= 1000");
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
			$number_of_rows = 0;
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			    foreach ($row as $item) {
		        	$number_of_rows = $item;
		    	}
			}
		
			$number_of_pages = round($number_of_rows/$rows_per_page);

		$page = $_GET["page"];
		if($page == "" or $page == "1"){
			$page1 = 0;
			$page2 = $page1 + 100;
		}
		else{
			$page1 = $page*$rows_per_page + 1;
			$page2 = $page1 + 99;
		}
		// Prepare the statement
		$stid = oci_parse($connection, "SELECT * from (SELECT rank()over(order by votes desc) as pozitie, id_song, name, link, votes, posted_time from SONGS) where pozitie >= '$page1' and pozitie <= '$page2'");
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
		for($i = 1; $i<$number_of_pages;$i++){

			?><a href="test_connection.php?page=<?php echo $i; ?>" style="text-decoration:none; margin-left: 140px; margin-right: -110px;"><?php echo $i. " "; ?> </a> <?php
		}

		// Fetch the results of the query
		print "<br><table border='1' align='center'>\n";
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
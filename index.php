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

<body style="background-color:#FAC8CD;">

	<!-- The omnipresent navbar -->
	<style>
		.nav > li > a:hover, .nav-default > li > a:focus {
	    background-color: #554E60;
	    color: #554E60;
	}
	</style>

	<div class="navbar-collapse collapse" style="background-color:#3C3744; margin-top: 0px;">
		<ul class="nav nav-justified">
			<li><a href="index.php" style="color: white">Home</a></li>
			<li><a href="login.php" style="color: white">Login</a></li>
			<li><a href="register.php" style="color: white">Register</a></li>
		</ul>
	</div>

	<!-- Here we check if the connection with the Oracle DB Server is working -->
	<div class="nav nav-justified text-center text-info bg-primary">
		<?php
			require_once(dirname(__FILE__) . '/functions.php');
			$connection = connect();

			if(!$connection) {
				echo '<br>Try to check if Oracle Services are up and running';
			}
		?>
	</div>


	<h3 class="text-center">Top 1000 - Voted Songs</h3>

	<div class="container text-center">
		<?php

			//Number of rows
			$rows_per_page = 100;
			$sql_stmt = "SELECT count(*) from (SELECT rank()over(order by votes desc) as pozitie, id_song, name, link, votes, posted_time from SONGS) where pozitie <= 1000";
			$stid = oci_parse($connection, $sql_stmt);
			
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
			else {
				$page1 = $page*$rows_per_page + 1;
				$page2 = $page1 + 99;
			}

			// Prepare the statement
			$sql_stmt = "SELECT * from (SELECT rank()over(order by votes desc) as pozitie, id_song, name, link, votes, posted_time from SONGS) where pozitie >= '$page1' and pozitie <= '$page2'";
			$stid = oci_parse($connection, $sql_stmt);

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

				?><a href="index.php?page=<?php echo $i; ?>"><?php echo $i. " "; ?> </a> <?php
			}

			// Fetch the results of the query
			print "<br><table border='1' align='center'>\n";
			print "<tr>\n";
			print "<td><strong>Rank</strong></td>
				<td><strong>ID Song</strong></td>
				<td><strong>Song</strong></td>
				<td><strong>Link</strong></td>
				<td><strong>Votes</strong></td>
				<td><strong>Published Date</strong></td>";
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
	</div>

</body>
</html>

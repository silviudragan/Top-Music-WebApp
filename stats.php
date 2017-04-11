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


	<h3 class="text-center">Testing Page</h3>

	<div class="container text-center">
		<?php

			$p_input = 'TEST';
			
			$sql_stmt = 'BEGIN testare_procedura.afisare( :p_input, :p_output ); END;';
			
			$stid = oci_parse($connection, $sql_stmt);

			if (!$stid) {
			    $e = oci_error($connection);
			    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}

			oci_bind_by_name($stid, ':p_input', $p_input, 32);

			oci_bind_by_name($stid, ':p_output', $p_output, 32);

			// Perform the logic of the query
			$r = oci_execute($stid);

			if (!$r) {
			    $e = oci_error($stid);
			    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}

			print "$p_output";
		?>
	</div>

	<div class="container text-center">
		<?php
			$p_username = 'user3091';

			$sql_stmt = 'BEGIN server_procedures.distributie( :p_username, :ref ); END;';

			$stid = oci_parse($connection, $sql_stmt);

			if (!$stid) {
			    $e = oci_error($connection);
			    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}

			$ref_cursor = oci_new_cursor($connection);

			oci_bind_by_name($stid, ':p_username', $p_username, -1);

			oci_bind_by_name($stid, ':ref', $ref_cursor, -1, OCI_B_CURSOR);

			if(oci_execute($stid)) {

				if(oci_execute($ref_cursor)) {

					print "<br><table border='1' align='center'>\n";
					print "<tr>\n";
					print "<td><strong>Genre</strong></td> <td><strong>Positive Votes %</strong></td>";
					print "</tr>";

					while ($row = oci_fetch_array($ref_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) {
					    print "<tr>\n";
					    foreach ($row as $item) {
					        print "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
					    }
					    print "</tr>\n";
					}
					print "</table>\n";
				}

				else {
					$e = oci_error();
					trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}
			}
			else {
				$e = oci_error();
				trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
		?>
	</div>

</body>
</html>


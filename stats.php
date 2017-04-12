<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Stats Page</title>
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
			<li><a href="stats.php" style="color: white">Stats</a></li>
			<li><a href="lucky.php" style="color: white">Lucky Songs</a></li>
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

	<div class="container text-center">

		<h3>Statistics for all users!</h3>

		<h4>Insert a username and an option and get the distribution of songs</h4>

		<?php
			// define variables and set to empty values
			$search = $searchErr = $option = $optionErr = $luckynumber = $luckynumberErr = "";
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			  if (empty($_POST["search"])) {
			    $searchErr = "<br>*the field is empty";
			  } else {
			    $search = test_input($_POST["search"]);
			  }
			  if (empty($_POST["option"])) {
			    $optionErr = "Must select an option";
			  } else {
			    $option = test_input($_POST["option"]);
			  }
			}
			function test_input($data) {
			  $data = trim($data);
			  $data = stripslashes($data);
			  $data = htmlspecialchars($data);
			  return $data;
			}
		?>

		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="text-align: center; margin-left:100px;">  
		  <input type="text" name="search">
		  <input type="submit" name="submit" value="Submit" style="width: 170px; margin-left: 10px;">
		  <br>
		  <input type="radio" name="option" value="posted_songs" checked> Songs Posted
		  <input type="radio" name="option" value="positive_votes"> Positive Votes
		  <input type="radio" name="option" value="negative_votes"> Negative Votes
		  <span class="error" style="margin-left: 60px; color: red;"><?php echo $searchErr;?></span> 
		</form>

		<?php

			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				$p_username = $search;
				
				if($option == "posted_songs"){
					$p_preference = 1;
				}

				if($option == "positive_votes"){
					$p_preference = 2;
				}

				if($option == "negative_votes"){
					$p_preference = 3;
				}

				$sql_stmt = 'BEGIN stats_page.distributie( :p_username, :p_preference, :ref ); END;';

				$stid = oci_parse($connection, $sql_stmt);

				if (!$stid) {
				    $e = oci_error($connection);
				    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}

				$ref_cursor = oci_new_cursor($connection);

				oci_bind_by_name($stid, ':p_username', $p_username, -1);

				oci_bind_by_name($stid, ':p_preference', $p_preference, -1);

				oci_bind_by_name($stid, ':ref', $ref_cursor, -1, OCI_B_CURSOR);

				if(oci_execute($stid)) {

					if(oci_execute($ref_cursor)) {

						print "<br><table border='1' align='center'>\n";
						print "<tr>\n";
						print "<td><strong>Genre</strong></td> 
							<td><strong>Positive Votes %</strong></td>
							<td><strong>Voted Songs</strong></td>";
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
			}
			
		?>
	</div>
	
</body>
</html>


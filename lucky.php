<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Lucky Page</title>
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

		<h3>Lucky Songs</h3>
		<h4>Insert a <strong>username</strong> and a <strong>number</strong> between 1 and 10000 and get 
		a list of songs that are in the genres liked by the user but were not voted yet by him</h4>	

		<?php
			// define variables and set to empty values
			$luckyuser = $luckynumber = $luckynumberErr = $luckyuserErr = "";
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			  if (empty($_POST["luckyuser"])) {
			    $luckyuserErr = "<br>*the field is empty";
			  } else {
			    $luckyuser = test_input($_POST["luckyuser"]);
			  }
			  if (empty($_POST["luckynumber"])) {
			    $luckynumberErr = "<br>*the field is empty";
			  } else {
			    $luckynumber = test_input($_POST["luckynumber"]);
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
			Username:<input type="text" name="luckyuser">
			Number:<input type="text" name="luckynumber">
			<input type="submit" name="submit2" value="Feeling Lucky" style="width: 170px; margin-left: 10px;">
		</form>

		<?php

			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				$p_username = $luckyuser;
				$p_number = $luckynumber;

				$sql_stmt = 'BEGIN stats_page.lucky( :p_username, :p_number, :ref ); END;';

				$stid = oci_parse($connection, $sql_stmt);

				if (!$stid) {
				    $e = oci_error($connection);
				    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}

				$ref_cursor = oci_new_cursor($connection);

				oci_bind_by_name($stid, ':p_username', $p_username, -1);

				oci_bind_by_name($stid, ':p_number', $p_number, -1);

				oci_bind_by_name($stid, ':ref', $ref_cursor, -1, OCI_B_CURSOR);

				if(oci_execute($stid)) {

					if(oci_execute($ref_cursor)) {

						print "<br><table border='1' align='center'>\n";
						print "<tr>\n";
						print "<td><strong>ID Song</strong></td> 
							<td><strong>Song Name</strong></td>
							<td><strong>Votes</strong></td>";
						print "</tr>";

						$is_data = false;
						while ($row = oci_fetch_array($ref_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) {
						    print "<tr>\n";
						    foreach ($row as $item) {
						        print "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
						        $is_data = true;
						    }
						    print "</tr>\n";
						}
						print "</table>\n";
						if($is_data == false){
							echo "<h4 style='text-align:center;'>No data found. </h4>";
						}
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


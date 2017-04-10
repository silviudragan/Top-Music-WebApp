<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
		$username = $_GET['user'];
	?>
	<title>Profile - <?php echo $username; ?></title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/home-page.css" rel="stylesheet">
</head>

<body style="background-color:#FAC8CD;">

	<!-- The omnipresent navbar -->
	<div class="navbar-collapse collapse" style="background-color:#3C3744; margin-top: 0px;">
		<ul class="nav nav-justified">
			<li><a href="meniu.php?user=<?php echo $username; ?>" style="color: white"> Home </a></li>
			<li><a href="profile.php?user=<?php echo $username; ?>" style="color: white">Profile</a></li>
			<li><a href="index.php" style="color: white">Logout</a></li>
		</ul>
	</div>

	<div class="nav nav-justified text-center text-info bg-primary">
		<?php
			require_once(dirname(__FILE__) . '/functions.php');
			$connection = connect();

			if(!$connection) {
				echo '<br>Try to check if Oracle Services are up and running';
			}
		?>
	</div>

	<br>
	<h3 style="margin-left: 150px;"> Profile </h3>
	<div class="divider" style="border-bottom: 1px solid #414242; margin: 20px 0px;"></div>

	<?php

		//Account type
			$stid = oci_parse($connection, "SELECT account_type from users where username='$username'");
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
			$result = 0;
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				foreach ($row as $item) {
				    $result = $item;
				}
			}
			$account_type = "user";
			if($result == 2){
				$account_type = "admin";
			}

		//Number of songs added
			$stid = oci_parse($connection, "SELECT count(s.id_user) from songs s
							  join users u on u.id = s.id_user
							  where u.username = '$username'");
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
			$number_of_songs = 0;
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				foreach ($row as $item) {
				    $number_of_songs = $item;
				}
			}
		//Can vote
			$stid = oci_parse($connection, "SELECT canvote from users where username='$username'");
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
			$result = 0;
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				foreach ($row as $item) {
				    $result = $item;
				}
			}
			$can_vote = "No";
			if($result == 1){
				$can_vote = "Yes";
			}
		//Join date
			$stid = oci_parse($connection, "SELECT date_created from users where username='$username'");
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
			$join_date = "";
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				foreach ($row as $item) {
				    $join_date = $item;
				}
			}

		echo "<h5 style='margin-left:90px;'> Username: <strong> $username </strong> </h5><br>
			  <h5 style='margin-left:90px;'> Account type: <strong> $account_type </strong> </h5><br>
			  <h5 style='margin-left:90px;'> Songs added: <strong> $number_of_songs </strong></h5><br>
			  <h5 style='margin-left:90px;'> Can vote: <strong> $can_vote </strong></h5><br>
			  <h5 style='margin-left:90px;'> Join date: <strong> $join_date </strong></h5><br>";
		echo   "<tr>
					<td class='contact-delete'>
    				<form action='delete.php?user=$username' method='post' style='margin-left:90px;'>
        				<input type='hidden' name='name' value='<?php echo $username; ?>''>
        				<input type='submit' name='submit' value='Delete account'>
    				</form>
					</td>                
                </tr>";

        echo "<br><a href='update.php?user=$username'><h4 style='margin-left:70px;'> Change password </h4></a>";

	?>
</body>
</html>

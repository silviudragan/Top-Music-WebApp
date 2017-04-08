<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/home-page.css" rel="stylesheet">

</head>

<body style="background-color:#ebebe0;">

	<?php
		$username = $_GET['user'];
	?>
	<div class="navbar-collapse collapse" style="background-color:#d6d6c2; margin-top: 0px;">
		<ul class="nav navbar-nav navbar-left" style="margin-left: 50px;">
			<li><a href="meniu.php?user=<?php echo $username; ?>"> Home </a></li>
			<li><a href="profile.php?user=<?php echo $username; ?>">Profile</a></li>
			<li><a href="test_connection.php">Logout</a></li>
		</ul>
	</div>
	<br>
	<h3 style="margin-left: 150px;"> Feed your curiosity </h3>
	<div class="divider" style="border-bottom: 1px solid #414242; margin: 20px 0px;"></div>

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

	?>

	<?php
		// define variables and set to empty values
		$earch = $searchErr = $option = $optionErr = "";

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


	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="text-align: left; margin-left:100px;">  
	  Search: <input type="text" name="search">
	  <input type="radio" name="option" value="male" checked> Name
	  <input type="radio" name="option" value="female"> Genre
	  <input type="radio" name="option" value="other"> Posted by
	  <input type="submit" name="submit" value="Submit" style="width: 170px; margin-left: 10px;"> 
	  <span class="error" style="margin-left: 60px; color: red;"><?php echo $searchErr;?></span> 
	</form>



	<?php
		// Prepare the statement
		if ($_SERVER["REQUEST_METHOD"] == "POST" and $option == "Name"){

			$query = "SELECT id_song, name, link, votes, posted_time from SONGS where NAME LIKE '%$search%'";
			echo $query;
			$stid = oci_parse($connection, $query);
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
			print "<td> ID Song </td><td> Song </td><td> Link </td><td> Votes </td><td> Published Date </td>";
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
		}
	?>

</body>

</html>
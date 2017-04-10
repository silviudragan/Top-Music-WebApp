<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update</title>
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
			<li><a href="menu.php?user=<?php echo $username; ?>" style="color: white"> Home </a></li>
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

			// define variables and set to empty values
			$usernameErr = $newpasswordErr = $passwordErr = $newpassword2Err = "";
			$username = $newpassword = $password = $newpassword2 = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			  if (empty($_POST["username"])) {
			    $usernameErr = "Username is required";
			  } else {
			    $username = test_input($_POST["username"]);
			  }

			  if (empty($_POST["password"])) {
			    $passwordErr = "Old password is required";
			  } else {
			    $password = test_input($_POST["password"]);
			  }
			  
			  if (empty($_POST["newpassword"])) {
			    $newpasswordErr = "New password is required";
			  } else {
			    $newpassword = test_input($_POST["newpassword"]);
			  }

			  if (empty($_POST["newpassword2"])) {
			    $newpassword2Err = "Confirm password is required";
			  } else {
			    $newpassword2 = test_input($_POST["newpassword2"]);
			  }

			}
			function test_input($data) {
			  $data = trim($data);
			  $data = stripslashes($data);
			  $data = htmlspecialchars($data);
			  return $data;
			}
		?>
	</div>

	<h2 style="text-align: center;">Change password</h2>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="text-align: center;">  
	  Username: <input type="text" name="username">
	  <span class="error"><?php echo $usernameErr;?></span>
	  <br><br>
	  Old password: <input type="password" name="password">
	  <span class="error"><?php echo $passwordErr;?></span>
	  <br><br>
	  New password: <input type="password" name="newpassword">
	  <span class="error"><?php echo $newpasswordErr;?></span>
	  <br><br>
	  Confirm password: <input type="password" name="newpassword2">
	  <span class="error"><?php echo $newpassword2Err;?></span>
	  <br><br>
	  <input type="submit" name="submit" value="Change password">
	  <input type="reset"> 
	</form>

	<?php
		// Prepare the statement
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$query = "UPDATE users set password='$newpassword' where username='$username'";
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
			else{
				echo "<script type = \"text/javascript\">
	                                    alert(\"Password changed...\");
	                                    window.location = (\"menu.php?user=$username\");
	                                    </script>"; 
			}
			oci_free_statement($stid);
		}

	?>


</body>



</html>

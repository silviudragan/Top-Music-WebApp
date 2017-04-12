<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
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

	<div class="nav nav-justified text-center text-info bg-primary">
		<?php
			require_once(dirname(__FILE__) . '/functions.php');
			$connection = connect();

			if(!$connection) {
				echo '<br>Try to check if Oracle Services are up and running';
			}

			// define variables and set to empty values
			$usernameErr = $passwordErr = "";
			$username = $password = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			  if (empty($_POST["username"])) {
			    $usernameErr = "Username is required";
			  } else {
			    $username = test_input($_POST["username"]);
			  }
			  
			  if (empty($_POST["password"])) {
			    $passwordErr = "Password is required";
			  } else {
			    $password = test_input($_POST["password"]);
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

	<div class="container text-center">
		<h2>Login</h2>

		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		  Username: <input type="text" name="username">
		  <span class="error"><?php echo $usernameErr;?></span>
		  <br><br>
		  Password: <input type="password" name="password">
		  <span class="error"><?php echo $passwordErr;?></span>
		  <br><br>

		  <div class="container text-center">
			<input class="btn btn-primary" type="submit" name="submit" value="Submit">
			<input class="btn btn-primary" type="reset">
		  </div>

		</form>
	</div>
	
	<?php
		// Prepare the statement
		if ($_SERVER["REQUEST_METHOD"] == "POST"){

			if(strpos($password, "'")){
				echo "<script type = \"text/javascript\">
	                                    alert(\"Possible hacker detected\");
	                                    window.location = (\"login.php\");
	                                    </script>";
			}
			$query = "SELECT * from USERS where USERNAME='$username' and PASSWORD='$password'";
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
			$valid = false;
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			    $valid = true;
			}
			if($valid == false){
				 echo "<script type = \"text/javascript\">
	                                    alert(\"Login failed...\");
	                                    window.location = (\"login.php\");
	                                    </script>"; 
			}
			else{
	            echo "<script type='text/javascript'>location.href = 'menu.php?user=$username';</script>";
			}

			oci_free_statement($stid);
		}
	?>
</body>
</html>

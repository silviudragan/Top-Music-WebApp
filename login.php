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

<body style="background-color:#ebebe0;">

	<div class="navbar-collapse collapse" style="background-color:#d6d6c2; margin-top: 0px;">
		<ul class="nav navbar-nav navbar-left" style="margin-left: 50px;">
			<li><a href="index.php"> Home </a></li>
			<li><a href="login.php">Login</a></li>
			<li><a href="register.php">Register</a></li>
		</ul>
	</div>

	<div class="container text-center text-info bg-danger">
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

	<h2 style="text-align: center;">Login</h2>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="text-align: center;">  
	  Username: <input type="text" name="username">
	  <span class="error"><?php echo $usernameErr;?></span>
	  <br><br>
	  Password: <input type="password" name="password">
	  <span class="error"><?php echo $passwordErr;?></span>
	  <br><br>
	  <input type="submit" name="submit" value="Submit">
	  <input type="reset"> 
	</form>

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
	            echo "<br><a href='meniu.php?user=$username'><h4 style='text-align: center'> Click to access the main page </h4></a>";
			}


			oci_free_statement($stid);
		}

	?>
</body>
</html>

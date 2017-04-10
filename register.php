<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register</title>
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
			$usernameErr = $emailErr = $passwordErr = $repasswordErr = "";
			$username = $email = $password = $repassword = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			  if (empty($_POST["username"])) {
			    $usernameErr = "Name is required";
			  } else {
			    $username = test_input($_POST["username"]);
			  }
			  
			  if (empty($_POST["password"])) {
			    $passwordErr = "Password is required";
			  } else {
			    $password = test_input($_POST["password"]);
			  }
			    
			  if (empty($_POST["repassword"])) {
			    $repasswordErr = "Confirm password is required";
			  } else {
			    $repassword = test_input($_POST["repassword"]);
			  }

			  if (empty($_POST["email"])) {
			    $email = "";
			  } else {
			    $email = test_input($_POST["email"]);
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

	<h2 style="text-align: center;">Register</h2>
	<p style="text-align: center;"><span class="error">* required field.</span></p>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="text-align: center;">  
	  Username: <input type="text" name="username">
	  <span class="error">* <?php echo $usernameErr;?></span>
	  <br><br>
	  Password: <input type="password" name="password">
	  <span class="error">* <?php echo $passwordErr;?></span>
	  <br><br>
	  Confirm password: <input type="password" name="repassword">
	  <span class="error">* <?php echo $repasswordErr;?></span>
	  <br><br>
	  E-mail: <input type="text" name="email">
	  <span class="error"><?php echo $emailErr;?></span>
	  <br><br>
	  <input type="submit" name="submit" value="Submit">  
	</form>

	<?php
		if($password != $repassword)
			{
				echo "<script type = \"text/javascript\">
	                                    alert(\"Passwords are different...\");
	                                    window.location = (\"register.php\");
	                                    </script>"; 
	        }
	      else{
			// Prepare the statement
			if ($_SERVER["REQUEST_METHOD"] == "POST"){

				$query = "SELECT max(id) from USERS";
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

				// Fetch the result of the query
				$max_id = 0;
				while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				    foreach ($row as $item) {
			        $max_id = $item;
			    	}
				}
				$max_id++;

				$query = "INSERT INTO users values($max_id, 1, '$username', '$password', 0, 0, sysdate)";
				$stid = oci_parse($connection, $query);
				if (!$stid) {
				    $e = oci_error($connection);
				    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}

				// Perform the logic of the query
				$r = oci_execute($stid);
				if (!$r) {
				    echo "<script type = \"text/javascript\">
	                                    alert(\"* fields must be completed\");
	                                    window.location = (\"register.php\");
	                                    </script>"; 
				}
				else{
					echo "<br> <h4 style='text-align: center'> Registration successful </h4>";
		            echo "<br><a href='meniu.php?user=$username'><h4 style='text-align: center'> Click to access the main page </h4></a>";
				}


				oci_free_statement($stid);
				}
			}
	?>

</body>



</html>

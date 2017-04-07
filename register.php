<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home Page</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/home-page.css" rel="stylesheet">

</head>

<body style="background-color:#ebebe0;">

	<div class="navbar-collapse collapse" style="background-color:#d6d6c2; margin-top: 0px;">
		<ul class="nav navbar-nav navbar-left" style="margin-left: 50px;">
			<li><a href="test_connection.php"> Home </a></li>
			<li><a href="login.php">Login</a></li>
			<li  class="active" style="color:#5c5c3d;"><a href="register.php">Register</a></li>
		</ul>
	</div>


	<?php
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
			echo "<h5 style='color:#ff0000;text-align:center;'>Passwords are different!</h5>";
		echo "<h2>Input:</h2>";
		echo $username;
		echo "<br>";
		echo $password;
		echo "<br>";
		echo $repassword;
		echo "<br>";
		echo $email;
	?>

</body>



</html>
<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Profile</title>
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
	<?php

		require_once(dirname(__FILE__) . '/functions.php');
		$connection = connect();

	?>
	<br>
	<h3 style="margin-left: 150px;"> Profile </h3>
	<div class="divider" style="border-bottom: 1px solid #414242; margin: 20px 0px;"></div>

	<?php

		echo "<h5 style='margin-left:90px;'> Username: </h5><br>
			  <h5 style='margin-left:90px;'> Account type: </h5><br>
			  <h5 style='margin-left:90px;'> Songs added: </h5><br>
			  <h5 style='margin-left:90px;'> Can vote: </h5><br>
			  <h5 style='margin-left:90px;'> Join date: </h5><br>";

	?>


</body>

</html>
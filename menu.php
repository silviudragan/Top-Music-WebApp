<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
		$username = $_GET['user'];
	?>
	<title>Menu - <?php echo $username; ?></title>
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
			<li><a href="menu.php?user=<?php echo $username; ?>" style="color: white">Home</a></li>
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
	<h3 class="text-center"> Feed your curiosity </h3>
	<div class="divider" style="border-bottom: 1px solid #414242; margin: 20px 0px;"></div>

	<?php
		// define variables and set to empty values
		$search = $searchErr = $option = $optionErr = "";
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
	  <input type="radio" name="option" value="name" checked> Name
	  <input type="radio" name="option" value="genre"> Genre
	  <input type="radio" name="option" value="posted"> Posted by
	  <input type="submit" name="submit" value="Submit" style="width: 170px; margin-left: 10px;"> 
	  <span class="error" style="margin-left: 60px; color: red;"><?php echo $searchErr;?></span> 
	</form>



	<?php
		// Prepare the statement
		if ($_SERVER["REQUEST_METHOD"] == "POST"){

			if($option == "name"){
				$query = "SELECT id_song, name, link, votes, posted_time from SONGS where lower(NAME) LIKE lower('%$search%')";
			}

			if($option == "genre"){
				$query = "SELECT s.id_song, s.name, s.link, s.votes, s.posted_time from SONGS s 
						join SONG_GENRE sg on s.id_song = sg.id_song
						join GENRES g on g.id_genre = sg.id_genre
						where lower(g.name) like lower('%$search%')";
			}

			if($option == "posted"){
				$query = "SELECT s.id_song, s.name, s.link, s.votes, s.posted_time from SONGS s
						join USERS u on u.id = s.id_user
						where lower(username) like lower('%$search%')";
			}

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
			print "<h3 style='text-align: center'> Results </h3>";
			print "<table border='1' align='center'>\n";
			print "<tr>\n";
			print "<td><strong>ID Song</strong></td>
				<td><strong>Song</strong></td>
				<td><strong> Link </strong></td>
				<td><strong> Votes </strong></td>
				<td><strong> Published Date </strong></td>";
			print "</tr>";

			$is_data = false;
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			    print "<tr>\n";
			    foreach ($row as $item) {
			        print "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
			        $is_data = true;
			    }
			    print "</tr>\n";
			}
			print "</table>\n";
			if($is_data == false){
				echo "<h4 style='text-align:center;'>No data found. </h4>";
			}

			oci_free_statement($stid);
		}
	?>
</body>
</html>

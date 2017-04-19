<?php
	
	require_once(dirname(__FILE__) . '/functions.php');
	$connection = connect();

	$username = $_GET['user'];
	$stid = oci_parse($connection, "DELETE from users where username='$username'");

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
	else
	{
		echo "<script type = \"text/javascript\">
	                                    alert(\"Account deleted...\");
	                                    window.location = (\"index.php\");
	                                    </script>"; 
	}

?>



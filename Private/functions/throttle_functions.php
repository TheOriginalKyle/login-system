<?php

// Brute force throttling

//They gave us some input but it didn't check out. We should write that down.
function record_failed_login($username) {

	//Lets give ourselves access to our database.
	$db = DBAccess::getMysqliConnection();

	//Sanitizing the input because what if these guys had malicious intent?
	$cleanEmail = mysqli_real_escape_string($db, $username);
	$count = 1;
	$last_time = time();

	//Lets see if they tried that username before.
	$failed_login = $db->query("SELECT * FROM failed_logins WHERE email='$cleanEmail'") or die($mysqli->error());

	//They never tried that username before.
	if($failed_login->num_rows < 1)
	{

		$sql = "INSERT INTO failed_logins (email, theCount, last_time)"
						. "VALUES ('$cleanEmail', '$count', '$last_time')";

		$db->query($sql);

	} else {
		// existing failed_login record
		$sqlArray = $failed_login->fetch_array(MYSQLI_ASSOC);
		$count = $sqlArray['theCount'] + 1;
		$sql = "UPDATE failed_logins SET theCount='$count', last_time='$last_time' WHERE email='$cleanEmail'";
		$db->query($sql);
	}

	return true;
}

//They either forgot their password or the hacker one either way gg.
function clear_failed_logins($username) {

	$db = DBAccess::getMysqliConnection();

	$cleanEmail = mysqli_real_escape_string($db, $username);
	$count = 0;
	$last_time = time();

	$failed_login = $db->query("SELECT * FROM failed_logins WHERE email='$cleanEmail'") or die($mysqli->error());

	//We should probably check that we wrote the attempt down.
	if($failed_login->num_rows > 0) {

		//Good news? We did.
		$sql = "UPDATE failed_logins SET (theCount, last_time) WHERE email='$cleanEmail'"
						. "VALUES ('$count', '$last_time')";
		$db->query($sql);
	}

	return true;
}

// Returns the number of minutes to wait until logins
// are allowed again.
function throttle_failed_logins($username) {
	$throttle_at = 5;
	$delay_in_minutes = 10;
	$delay = 60 * $delay_in_minutes;

	$db = DBAccess::getMysqliConnection();

	$cleanEmail = mysqli_real_escape_string($db, $username);

	$failed_login = $db->query("SELECT * FROM failed_logins WHERE email='$cleanEmail'") or die($mysqli->error());
	$sqlArray = $failed_login->fetch_array(MYSQLI_ASSOC);

	// Once failure count is over $throttle_at value,
	// user must wait for the $delay period to pass.
	if($failed_login->num_rows >= 1 && $sqlArray['theCount'] >= $throttle_at) {
		$remaining_delay = ($sqlArray['last_time'] + $delay) - time();
		$remaining_delay_in_minutes = ceil($remaining_delay / 60);
		return $remaining_delay_in_minutes;
	} else {
		return 0;
	}
}

?>

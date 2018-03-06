<?php

// Brute force throttling

function record_failed_login($username) {

	$db = DBAccess::getMysqliConnection();

	$cleanEmail = mysqli_real_escape_string($db, $username);
	$count = 1;
	$last_time = time();

	$failed_login = $db->query("SELECT * FROM failed_logins WHERE email='$cleanEmail'") or die($mysqli->error());

	if($failed_login->num_rows < 1)
	{

		$sql = "INSERT INTO failed_logins (username, theCount, last_time)"
						. "VALUES ('$cleanEmail', '$count', '$last_time')";

		$db->query($sql);

	} else {
		// existing failed_login record
		$count = $failed_login['theCount'] + 1;
		$sql = "UPDATE failed_logins SET (theCount, last_time) WHERE email='$cleanEmail'"
						. "VALUES ('$count', '$last_time')";
		$db->query($sql);
	}

	return true;
}

function clear_failed_logins($username) {

	$db = DBAccess::getMysqliConnection();

	$cleanEmail = mysqli_real_escape_string($db, $username);
	$count = 0;
	$last_time = time();

	$failed_login = $db->query("SELECT * FROM failed_logins WHERE email='$cleanEmail'") or die($mysqli->error());

	if($failed_login->num_rows > 0) {

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

	// Once failure count is over $throttle_at value,
	// user must wait for the $delay period to pass.
	if($failed_login->num_rows > 0 && $failed_login['theCount'] >= $throttle_at) {
		$remaining_delay = ($failed_login['last_time'] + $delay) - time();
		$remaining_delay_in_minutes = ceil($remaining_delay / 60);
		return $remaining_delay_in_minutes;
	} else {
		return 0;
	}
}

?>

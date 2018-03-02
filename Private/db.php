<?php

// A more secure way to connect
require_once("DBAccess.php");

$db = DBAccess::getMysqliConnection();
if ($db != null) {
    $db = DBAccess::closeConnection($db);
}

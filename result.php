<?php
error_reporting(0);
$db_filename = "../db/db.sqlite";
if($db = sqlite_open($db_filename, 0777)) {
	$user_id = sqlite_escape_string($_POST['user_id']);
	$name = sqlite_escape_string($_POST['name']);
	$value = sqlite_escape_string($_POST['value']);
	sqlite_query($db, "INSERT INTO responses (user_id, id, value, created_on) VALUES ('$user_id', '$name', '$value', datetime('now'))");
	echo sqlite_changes($db);
	sqlite_close($db);
}
?>
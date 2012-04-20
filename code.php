<?php
error_reporting(0);
$db_filename = "../db/db.sqlite";
if($db = sqlite_open($db_filename, 0666)) {
	$user_id = sqlite_escape_string($_GET['user_id']);
	$query = sqlite_query($db, "SELECT code FROM users WHERE id = '$user_id'");
	$user = sqlite_fetch_array($query, SQLITE_ASSOC);
	sqlite_close($db);
}
if($user === false) {
	exit("Error.");
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Personalized Web Search Survey</title>
		<style>
			body {
				text-align: center;
				background: #DDD;
			}
			.content {
				text-align: left;
				margin: 0 auto;
				max-width: 700px;
				padding: 20px 10px;
				background: #FFF;
				border: 1px solid #241522;
			}
			h1 {
				font-size: 400%;
				margin: 0;
			}
			p {
				font-size: 150%;
			}
			.footnote {
				font-size: 90%;
				color: #333;
			}
			label, input {
				font-size: 200%;
			}
			label {
				margin-top: 0.5em;
				display: block;
			}
			#name, #email {
				background: #EFEFEF;
				border: 1px solid #A3A3A3;
			}
		</style>
	</head>
	<body>
		<div class="content">
			<p>Thank you for your participation. You code is: <?php echo $user['code'] ?></p>
			<p>Please arrange a time to meet with the researcher to exchange this code for your renumeration.</p> 
		</div>
	</body>
</html>

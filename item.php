<?php
error_reporting(0);
$db_filename = "../db/db.sqlite";
if($db = sqlite_open($db_filename, 0666, $sqlite_error)) {
	$user_id = sqlite_escape_string($_GET['user_id']);
	$question_id = sqlite_escape_string($_GET['id']);
	$query = sqlite_query($db, "SELECT id, title, description, url FROM questions WHERE id = '$question_id'");
	$question = sqlite_fetch_array($query, SQLITE_ASSOC);
	$query = sqlite_query($db, "SELECT value FROM responses WHERE user_id = '$user_id' AND id = '{$question['id']}B'  ORDER BY created_on ASC");
	$response = sqlite_fetch_array($query, SQLITE_ASSOC);
	sqlite_close($db);

	$question['response'] = $response['value'];
}
if($question === false) {
  exit("Error.");
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="combo.min.css">
		<title>Personalized Web Search Survey</title>
		<style type="text/css">
			.rating p {
				display: inline;
			}
			.rating {
				border-bottom: 1px solid #000;
				padding: 3px 0;
			}
			body {
				margin: 0;
			}
			table {
				border-spacing: 0;
			}
			td {
				padding: 0;
			}
			table, iframe {
				width: 100%;
				height: 100%;
			}
			label.ui-widget span {
				font-size: 0.7em;
			}
			#tiptip_content {
				font-size: 0.8em;
			}
		</style>
		<script type="text/javascript" src="combo.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("input").change(function(event) {
					$.post('result.php', {
						'user_id': '<?php echo $user_id ?>',
						'name': $(event.target).attr('name'),
						'value': $(event.target).val()
					}, function(data) {
						console.log(data)
            self.location.href = "list.php?user_id=<?php echo $user_id ?>#<?php echo $question_id ?>"
					})
				})

				$("label.positive").tipTip({ content: "Looks interesting" })
				$("label.neutral").tipTip( { content: "Meh" })
				$("label.negative").tipTip({ content: "Looks boring" })
				$(".rating").buttonset()
				$("input:checked").trigger('click')
			})
		</script>
	</head>
	<body>
		<table>
			<tr style="height:20px"><td>
				<div class="rating">
					<p>Page rating:</p>
					<input type="radio" id="<?php echo $question["id"] ?>B_2" name="<?php echo $question["id"] ?>B"<?php if($question['response'] == "1") echo " checked=\"checked\"" ?> value="1">
					<label class="positive" for="<?php echo $question["id"] ?>B_2">+1</label>

					<input type="radio" id="<?php echo $question["id"] ?>B_1" name="<?php echo $question["id"] ?>B"<?php if($question['response'] == "0") echo " checked=\"checked\"" ?> value="0">
					<label class="neutral" for="<?php echo $question["id"] ?>B_1">0</label>

					<input type="radio" id="<?php echo $question["id"] ?>B_0" name="<?php echo $question["id"] ?>B"<?php if($question['response'] == "-1") echo " checked=\"checked\"" ?> value="-1">
					<label class="negative" for="<?php echo $question["id"] ?>B_0">-1</label>
				</div>
			</td></tr>
			<tr><td id="frame"><iframe frameborder="0" src="<?php echo $question["url"] ?>"></iframe></td></tr>
		</table>
	</body>
</html>

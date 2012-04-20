<?php
error_reporting(0);
$db_filename = "../db/db.sqlite";
if($db = sqlite_open($db_filename, 0666)) {
	$user_id = sqlite_escape_string($_GET['user_id']);
	$query = sqlite_query($db, "SELECT questions_order FROM users WHERE id = '$user_id'");
	$user = sqlite_fetch_array($query, SQLITE_ASSOC);
	$query = sqlite_query($db, "SELECT id, title, description, url FROM questions");
	$questions = sqlite_fetch_all($query, SQLITE_ASSOC);
	$query = sqlite_query($db, "SELECT id, value FROM responses WHERE user_id = '$user_id' ORDER BY created_on ASC");
	$responses = sqlite_fetch_all($query, SQLITE_ASSOC);
	sqlite_close($db);
	$user['questions_order'] = explode(',', $user['questions_order']);

	$qs = array();
	foreach($user['questions_order'] as $question_id) {
		$question = $questions[$question_id];
		$qs[] = $question;
	}
	$questions = $qs;

	$rs = array();
	foreach($responses as $response) {
		$rs[$response['id']] = $response['value'];
	}
	$responses = $rs;

	for($i = 0; $i < count($questions); $i++) {
		$questions[$i]['response'] = $responses["{$questions[$i]['id']}A"];
		$questions[$i]['title'] = str_replace("''", "'", $questions[$i]['title']);
		$questions[$i]['description'] = str_replace("''", "'", $questions[$i]['description']);
	}
}
if($user === false || $questions === false) {
  exit("Error.");
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Personalized Web Search Survey</title>
		<link rel="stylesheet" type="text/css" href="combo.min.css">
		<style type="text/css">
			li {
				list-style: none;
				max-width: 900px;
			}
			.rating p {
				margin: 0;
			}
			.rating {
				float: left;
				margin-top: 46px;
				margin-left: 23px;
				width: 160px
			}
			.content {
				margin-left: 200px;
			}
			a.blurred {
				text-shadow: 0 0 6px #999;
				color: transparent;
			}
			.link {
				position: relative;
			}
			p.fake-link {
				background: url(images/blurred-url.png) no-repeat;
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
					})
				})

				$("#code").button()
				$("label.positive").tipTip({ content: "Looks interesting" })
				$("label.neutral").tipTip( { content: "Meh" })
				$("label.negative").tipTip({ content: "Looks boring" })
				$(".rating").buttonset()
				$('input').click(function(event) {
					$(event.target).closest('li').find('p.fake-link').hide()
					$(event.target).closest('li').find('p.real-link').show()
				})
				$("p.real-link").hide()
				$("input:checked").trigger('click')
			})
		</script>
	</head>
	<body>
		<ul>
		<?php foreach($questions as $question): ?>
			<li>
				<div class="rating">
					<p>Description rating:</p>
					<input type="radio" id="<?php echo $question["id"] ?>A_2" name="<?php echo $question["id"] ?>A"<?php if($question['response'] == "1") echo " checked=\"checked\"" ?> value="1">
					<label class="positive" for="<?php echo $question["id"] ?>A_2">+1</label>

					<input type="radio" id="<?php echo $question["id"] ?>A_1" name="<?php echo $question["id"] ?>A"<?php if($question['response'] == "0") echo " checked=\"checked\"" ?> value="0">
					<label class="neutral" for="<?php echo $question["id"] ?>A_1">0</label>

					<input type="radio" id="<?php echo $question["id"] ?>A_0" name="<?php echo $question["id"] ?>A"<?php if($question['response'] == "-1") echo " checked=\"checked\"" ?> value="-1">
					<label class="negative" for="<?php echo $question["id"] ?>A_0">-1</label>
				</div>
				<div class="content">
					<h2 id="<?php echo $question["id"] ?>"><?php echo $question["title"] ?></h2>
					<div class="description"><?php echo $question["description"] ?></div>
					<div style="clear:both"class="link">
						<p class="real-link">Go to page to rate: <a href="item.php?id=<?php echo $question["id"] ?>&user_id=<?php echo $user_id ?>"><?php echo $question["url"] ?></a></p>
						<p class="fake-link">Please rate description before rating page.</p>
					</div>
				</div>
			</li>
		<?php endforeach ?>
		</ul>
		<p>Once you are done click "Submit" to retrieve code. Arrange a time to meet with the researcher and exchange code for renumeration.</p>
		<p><a id="code" href="code.php?user_id=<?php echo $user_id ?>">Submit</a></p>
	</body>
</html>

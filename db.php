#!/usr/bin/php
<?php
$db_filename = dirname(dirname(__FILE__)) . "/db/db.sqlite";
function rand_id() {
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
    shuffle($chars);
    return implode("", array_slice($chars, 0, 6));
}

function rand_code() {
    $chars = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');
    shuffle($chars);
    return implode("", array_slice($chars, 0, 4));
}

function add_user($questions_count) {
    $question_ids = range(0, $questions_count-1);
    shuffle($question_ids);
    $question_ids = implode(",", $question_ids);
    $id = rand_id();
    $code = rand_code();
    echo "INSERT INTO users (id, code, questions_order) VALUES ('{$id}', '{$code}', '{$question_ids}');\n";
}

function add_questions($filename) {
    $items = json_decode(file_get_contents($filename));

    foreach($items as $index => $item) {
        echo "INSERT INTO questions (id, title, description, url) VALUES ('${index}', '".sqlite_escape_string($item->title)."', '".sqlite_escape_string($item->description)."', '{$item->url}');\n";
    }
}

function init() {
    echo "CREATE TABLE IF NOT EXISTS users (id TEXT PRIMARY KEY, code TEXT, questions_order TEXT);\n";
    echo "CREATE TABLE IF NOT EXISTS questions (id INTEGER PRIMARY KEY, title TEXT, description TEXT, url TEXT);\n";
    echo "CREATE TABLE IF NOT EXISTS responses (user_id TEXT, id INTEGER, value INTEGER, created_on TEXT, PRIMARY KEY (user_id, id, created_on));\n";
}

function id_to_index($id) {
    preg_match('/(\d+)(A|B)/', $id, $matches);
    $i = ((int)$matches[1]) * 2;
    if($matches[2] == 'A') {
        $i += 0;
    } else if($matches[2] == 'B') {
        $i += 1;
    }
    return $i;
}

function results($db_filename) {
    $str_values = array(-1 => '-', 0 => '0', 1 => '+');
    $count = 0;
    if($db = sqlite_open($db_filename, 0666, $sqlite_error)) {
        $query = sqlite_query($db, "SELECT id FROM users");
        $users = sqlite_fetch_all($query, SQLITE_ASSOC);
        foreach($users as $user) {
            $result = array_fill(0, 100, '_');
            $query = sqlite_query($db, "SELECT id, value, created_on FROM responses WHERE user_id = '{$user['id']}' ORDER BY created_on");
            $responses = sqlite_fetch_all($query, SQLITE_ASSOC);
            foreach($responses as $response) {
                $value = $str_values[(int)$response['value']];
                $i = id_to_index($response['id']);
                $result[$i] = $value;
            }
            $duration = strtotime($responses[count($responses)-1]['created_on']) - strtotime($responses[0]['created_on']);
            $duration = round($duration / 60.0);
            $done = substr_count(implode('', $result), '_') <= 5;
            echo "{$user['id']}:" . implode('', $result) . ":{$duration} min:{$done}\n";
            if($done) {
                $count += 1;
            }
        }
        echo "{$count}\n";
        sqlite_close($db);
    } else {
        echo "{$sqlite_error}\n";
    }
}

if(php_sapi_name() != "cli") {
    exit("Must be run on the command line\n");
}
$help_text = "options: add_user [QUESTIONS_COUNT], init, add_questions JSON_FILE, results\n";
$exe = array_shift($argv);
$cmd = array_shift($argv);
if(!$cmd) {
    exit($help_text);
}

switch($cmd) {
    case "add_user":
    $questions_count = array_shift($argv);
    if(!$questions_count) {
        $questions_count = 50;
    }
    add_user($questions_count);
    break;
    case "init":
    init();
    break;
    case "add_questions":
    $file = array_shift($argv);
    if(!$file) {
        exit("option add_questions: need to specify filename\n");
    }
    add_questions($file);
    break;
    case "results":
    results($db_filename);
    break;
    default:
    echo $help_text;
}
?>

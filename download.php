<?php

define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPASS", "root");
define("DBNAME", "test-board");

$csv_data = null;
$sql = null;
$pdo = null;
$option = null;
$message_array = array();

session_start();

if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
    //ここファイル作成と出力する処理(これは時間があまるときに実装しよう)
} else {
    header("Location: ./admin.php");
    exit;
}

return; #ページを表示したいため。明示的に記述。

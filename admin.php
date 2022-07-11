<?php

define('PASSWORD', 'admin');

define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPASS", "root");
define("DBNAME", "test-board");

date_default_timezone_set("Asia/Tokyo");

//変数の初期化
$current_date = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();
$pdo = null;
$stmt = null;
$res = null;
$option = null;

//セッションをはじめるとき
session_start();

//データベース接続
try {
    $pdo = new PDO('mysql:charset=UTF8;dbname=' . DBNAME . ';host=' . DBHOST, DBUSER, DBPASS);
} catch (PDOException $e) {
    //接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
}

//送信して受け取ったデータは$_POSTの中に自動的に入る。
//投稿データがあるときだけログを表示する。
if (!empty($_POST["submitButton"])) {
    if (!empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD) {
        $_SESSION['admin_login'] = true;
    } else {
        $error_message[] = 'ログインに失敗しました。';
    }
}

if (empty($error_message)) {
    //DBからコメントデータを取得する
    $sql = "SELECT username, comment, post_date FROM comment ORDER BY post_date ASC";
    $message_array = $pdo->query($sql);
}

//DB接続を閉じる
$pdo = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者専用ページ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1 class="title">管理者専用ページ</h1>
    <hr>

    <!-- バリデーションチェック時 -->
    <?php if (!empty($error_message)) : ?>
        <ul class="errorComment">
            <?php foreach ($error_message as $value) : ?>
                <li><?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) : ?>

        <!-- <form action="./download.php" method="GET">
            <input type="submit" name="downloadButton" value="ダウンロード" class="downloadButton">
        </form> -->

        <div class="boardWrapper">
            <div class="childWrapper">
                <div class="threadTitle">
                    <span>【タイトル】</span>
                    <h1>２チャンネル作ってみたｗｗｗ</h1>
                </div>
                <section>
                    <?php if (!empty($message_array)) : ?>
                        <?php foreach ($message_array as $value) : ?>
                            <article>
                                <div class="wrapper">
                                    <div class="nameArea">
                                        <span>名前：</span>
                                        <p class="username"><?php echo $value['username'] ?></p>
                                        <time>：<?php echo date('Y/m/d H:i', strtotime($value['post_date'])); ?></time>
                                    </div>
                                    <p class="comment"><?php echo $value['comment']; ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </div>

    <?php else : ?>
        <!-- ここにログインフォームが入る -->
        <form method="post">
            <div>
                <label for="admin_password">パスワード</label>
                <input type="password" name="admin_password">
            </div>
            <input type="submit" name="submitButton" value="ログイン">
        </form>

    <?php endif; ?>
</body>

</html>
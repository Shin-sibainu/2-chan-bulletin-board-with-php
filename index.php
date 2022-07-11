<?php

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

    //任意
    // $option = array(
    //     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //     PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
    // );

    $pdo = new PDO('mysql:charset=UTF8;dbname=' . DBNAME . ';host=' . DBHOST, DBUSER, DBPASS);
} catch (PDOException $e) {
    //接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
}

//送信して受け取ったデータは$_POSTの中に自動的に入る。
//投稿データがあるときだけログを表示する。
if (!empty($_POST["submitButton"])) {

    //表示名の入力チェック
    if (empty($_POST["username"])) {
        $error_message[] = "お名前を入力してください。";
    } else {
        $clean['username'] = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
        // $_SESSION['username'] = $_POST["username"];
    }

    //コメントの入力チェック
    if (empty($_POST["comment"])) {
        $error_message[] = "コメントを入力してください。";
    } else {
        $clean['comment'] = htmlspecialchars($_POST["comment"], ENT_QUOTES, "UTF-8");
    }

    //エラーメッセージが何もないときだけデータ保存できる
    if (empty($error_message)) {
        // var_dump($_POST);

        //ここからDB追加のときに追加
        $current_date = date("Y-m-d H:i:s");

        //トランザクション開始
        $pdo->beginTransaction();

        try {

            //SQL作成
            $stmt = $pdo->prepare("INSERT INTO comment (username, comment, post_date) VALUES (:username, :comment, :current_date)");

            //値をセット
            $stmt->bindParam(':username', $clean["username"], PDO::PARAM_STR);
            $stmt->bindParam(':comment', $clean["comment"], PDO::PARAM_STR);
            $stmt->bindParam(':current_date', $current_date, PDO::PARAM_STR);

            //SQLクエリの実行
            $res = $stmt->execute();

            //ここまでエラーなくできたらコミット
            $res = $pdo->commit();
        } catch (Exception $e) {
            //エラーが発生したときはロールバック(処理取り消し)
            $pdo->rollBack();
        }

        if ($res) {
            $success_message = "コメントを書き込みました。";
        } else {
            $error_message[] = "書き込みに失敗しました。";
        }

        $stmt = null;
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
    <title>2チャンネル掲示板</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1 class="title">2チャンネル掲示板</h1>
    <hr>

    <!-- メッセージ送信成功時 -->
    <?php if (!empty($success_message)) : ?>
        <p class="success_message"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <!-- バリデーションチェック時 -->
    <?php if (!empty($error_message)) : ?>
        <ul class="errorComment">
            <?php foreach ($error_message as $value) : ?>
                <li><?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

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
            <form method="POST" action="" class="formWrapper">
                <div>
                    <input type="submit" value="書き込む" name="submitButton">
                    <label for="usernameLabel">名前：</label>
                    <input type="text" name="username" value="<?php if (!empty($_SESSION['username'])) {
                                                                    echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
                                                                }    ?>">
                </div>
                <div>
                    <textarea name="comment" class="commentTextArea"></textarea>
                </div>
            </form>
        </div>
    </div>

    <div class="newThreadWrapper">
        <div class="newChildThreadWrapper">
            <input type="submit" value="新規スレッド書き込み">
        </div>
    </div>
</body>

</html>
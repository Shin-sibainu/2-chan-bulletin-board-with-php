<?php

//メッセージを保存するファイルのパス設定
define('FILENAME', "./message.txt");

date_default_timezone_set("Asia/Tokyo");

//変数の初期化
$current_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();

//送信して受け取ったデータは$_POSTの中に自動的に入る。
//投稿データがあるときだけログを表示する。
if (!empty($_POST["submitButton"])) {

    //表示名の入力チェック
    if (empty($_POST["username"])) {
        $error_message[] = "お名前を入力してください。";
    }

    //コメントの入力チェック
    if (empty($_POST["comment"])) {
        $error_message[] = "コメントを入力してください。";
    }

    //エラーメッセージが何もないときだけデータ保存できる
    if (empty($error_message)) {
        // var_dump($_POST);
        if ($file_handle = fopen(FILENAME, "a")) {
            $current_date = date("Y-m-d H:i:s");

            //書き込むデータを作成
            $data = "'" . $_POST["username"] . "','" . $_POST["comment"] . "','" . $current_date . "'\n";

            //書き込み
            fwrite($file_handle, $data);

            fclose($file_handle);

            $success_message = 'コメントを書き込みました';
        }
    }
}

//ファイルの中身を見に行く
if ($file_handle = fopen(FILENAME, "r")) {
    while ($data = fgets($file_handle)) {

        $split_data = preg_split('/\'/', $data);

        $message = array(
            "username" => $split_data[1],
            "comment" => $split_data[3],
            "post_date" => $split_data[5]
        );
        array_unshift($message_array, $message);

        // echo $data . "<br>";
    }

    fclose($file_handle);
}
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
                <h1>今、○○だけど質問ある？</h1>
            </div>
            <hr>
            <section>
                <?php if (!empty($message_array)) : ?>
                    <?php foreach ($message_array as $value) : ?>
                        <article>
                            <div class="info">
                                <h2><?php echo $value['username'] ?></h2>
                                <p><?php echo $value['comment']; ?></p>
                                <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
            <form method="POST" action="">
                <div>
                    <label for="username">お名前</label>
                    <input type="text" name="username">
                </div>
                <div>
                    <label for="comment">コメントを書き込む</label>
                    <textarea name="comment"></textarea>
                </div>
                <input type="submit" value="投稿" name="submitButton">
            </form>
        </div>
    </div>

    <div class="newThreadWrapper">
        <div class="newChildThreadWrapper">

        </div>
    </div>
</body>

</html>
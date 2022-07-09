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
$clean = array();

//送信して受け取ったデータは$_POSTの中に自動的に入る。
//投稿データがあるときだけログを表示する。
if (!empty($_POST["submitButton"])) {

    //表示名の入力チェック
    if (empty($_POST["username"])) {
        $error_message[] = "お名前を入力してください。";
    } else {
        $clean['username'] = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
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
        if ($file_handle = fopen(FILENAME, "a")) {
            $current_date = date("Y-m-d H:i:s");

            //書き込むデータを作成
            // $data = "'" . $_POST["username"] . "','" . $_POST["comment"] . "','" . $current_date . "'\n";
            $data = "'" . $clean["username"] . "','" . $clean["comment"] . "','" . $current_date . "'\n";

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
                    <input type="text" name="username">
                </div>
                <div>
                    <textarea name="comment" class="commentTextArea"></textarea>
                </div>
            </form>
        </div>
    </div>
    <div class="boardWrapper">
        <div class="childWrapper">
            <div class="threadTitle">
                <span>【タイトル】</span>
                <h1>PHPとMySQLで作っています</h1>
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
                    <input type="text" name="username">
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
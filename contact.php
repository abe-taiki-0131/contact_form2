<?php
// phpMailerの使用宣言
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    // POSTでのアクセスでない場合
    $name = '';
    $email = '';
    $subject = '';
    $message = '';
    $err_msg = array();
    $complete_msg = '';

} else {
    // フォームがサブミットされた場合( POST処理 )
    // 入力された値を取得
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // エラーメッセージ・完了メッセージの用意
    $err_msg = array();
    $complete_msg = '';

    // 空チェック
    if ( $name=='' || $email=='' || $subject=='' || $message=='' ) {
        $err_msg[] = "全ての項目を入力してください";
    
    // サニタイズ
    } else {
        $name = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $email = htmlspecialchars( $email, ENT_QUOTES, "UTF-8" );
        $subject = htmlspecialchars( $subject, ENT_QUOTES, "UTF-8" );
        $message = htmlspecialchars( $message, ENT_QUOTES, "UTF-8" );

        // 名前の長さチェック
        if ( mb_strlen($name) > 20 ) {
            $err_msg[] = '「名前」は20字以内で入力してください';
        }
        // メール形式チェック( 正規表現使用 )
        $reg_str = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
        if ( !preg_match($reg_str, $email) ) {
            $err_msg[] = "「メールアドレス」は正しい形式で入力してください";
        }
        // 件名の長さチェック
        if ( mb_strlen($subject) > 30 ) {
            $err_msg[] = '「件名」は30字以内で入力してください';
        }
        // 本文の長さチェック
        if ( mb_strlen($message) > 200 ) {
            $err_msg[] = '「本文」は200字以内で入力してください';
        }
    }

    // エラーなし
    if ( count($err_msg)==0 ) {
        
        $mail = new PHPMailer(true);
        // 言語設定、内部エンコーディングの指定
        mb_language('japanese');
        mb_internal_encoding("UTF-8");

        $mail = new PHPMailer(true);

        try {
            //Gmail 認証情報
            $host = 'smtp.gmail.com';
            $username = 'kyth.abe.cham0908@gmail.com'; // example@gmail.com
            $password = 'Abetai2525';

            //差出人
            $from = $email;
            $fromname = $name;

            //宛先
            $to = 'kyth.abe.cham0908@gmail.com';
            $toname = "受取人の名前";

            //件名・本文
            $body = '以下の内容でお問い合わせがありました。'."\r\n";
            $body .= "名前: " . $name ."\r\n";
            $body .= "メールアドレス: " . $email ."\r\n";
            $body .= "お問い合わせ内容: " . $message;

            //メール設定
            // $mail->SMTPDebug = 2; //デバッグ用
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $host;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = "utf-8";
            $mail->Encoding = "base64";
            $mail->setFrom($from, $fromname);
            $mail->addAddress($to, $toname);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            //メール送信
            $mail->send();
            // 完了メッセージ
            $complete_msg = '送信されました！';

            // 全てクリア
            $name = '';
            $email = '';
            $subject = '';
            $message = '';

        } catch (Exception $e) {
            $err_msg[] = "送信に失敗しました";
        }
    }
}
?>

<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">
    <style type="text/css">
        body {
            background-color:#f3f3f3;
        }
        .container {
            font-family:"Noto Sans JP";
            margin-top: 60px;
        }
        .row {
            text-align: center;
        }
        h1 {
            margin-bottom: 50px;
            text-align: center;
        }
        button {
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="mx-auto" style="width: 300px;">
    <div class="container">
        <div class="row">
            <div class="col-xs-offset-4 col-xs-4">
                <h1>お問い合わせ</h1>

                <?php if( count($err_msg) != 0 ): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($err_msg as $value): ?>
                            <?php echo '・'.$value."<br>"; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if( $complete_msg != "" ): ?>
                    <div class="alert alert-success">
                        <?php echo $complete_msg; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="お名前" value="<?php echo $name; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="email" placeholder="メールアドレス" value="<?php echo $email; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="件名" value="<?php echo $subject; ?>">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="message" rows="5" placeholder="本文"><?php echo $message; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">送信</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php

include_once("../config.php");
include_once("../common.php");

 /*DEBUG CODE*/
session_start();
echo ("sessionKey::sessionKey = ".$_SESSION['sessionKey']);
echo nl2br("\n");
echo("sessionKey::uploadkey = ".$_SESSION['uploadKey']);
echo nl2br("\n");
echo("sessionKey::developer_mail = " .$_SESSION['developer_mail']);
///////////////


?>

<!doctype html>
<html lang = "ja">
<head>
    <meta charset="utf-8">
    <title>Upload Page</title>
</head>
<body>
<pre>
<font face="Meiryo UI">
<h1>アップロードページ</h1>
<form method="post" action="upload.php" enctype="multipart/form-data">
<!-- <form method="post" action="./tmp/session.php" enctype="multipart/form-data"> -->
<font color="red"><br>
<?php 
	if (isset($_GET['errorcode'])){
		$e = $_GET['errorcode'];
		switch($e){

			case 8:
			echo "認証に失敗しました。Cokkieを有効にしてください。";
			break;

			case 9:
			echo "認証に失敗しました。アップロードキーを確認してください。";
			break;

			case 14:
			echo "アップロードされたコンテンツのファイルサイズが大きすぎます。最大２GBです。";
			break;

			default:
			break;
		}
	}
?>
</font>
<b><u>①コンテンツの描画手法を選択してください。</u></b><br>
 Select Rendering method of program. <br>
	<input type="radio" name="rendering_type" value="DirectX9" required>DirectX9
	<input type="radio" name="rendering_type" value="DirectX11">DirectX11
	<input type="radio" name="rendering_type" value="OpenGL">OpenGL

	<br><b><u>②アップロードするコンテンツを選択してください。(※最大2ギガバイトまで)</u></b><br>
 Select File. (2GBytes in Max) <br>
<!--	<input type="hidden" name="MAX_FILE_SIZE" value="1073741824" /> -->
	<input type="hidden" name="MAX_FILE_SIZE" value="2147483648" />
	<input type="file" name="userfile" size="30" required><br><br>
<!--
	<br><b><u>③メールアドレスとアップロードキーを入力してください</u></b><br>
 Enter your E-mail address and Upload Key. <br>
	メールアドレス ： <br>
	<input pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" type="email" placeholder="aaa@exmaple.co.jp" name="developer_mail" size="50"><br>
	アップロードキー ： 
	<input maxlength="8" pattern="^([a-zA-Z0-9]{8})$" type="password" name="upload_password" size="10"><br><br>

<b><u>③アップロードキーを入力してください</u></b><br>
	アップロードキー ： 
	<input maxlength="8" pattern="^([a-zA-Z0-9]{8})$" type="password" name="upload_password" size="10"><br><br>
	-->
	<input type="submit" value="送信">


</form>
</font>
</pre>
</body>
</html>
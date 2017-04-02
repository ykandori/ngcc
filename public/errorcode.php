<?php

/*
エラーコードとユーザ向けエラーメッセージの一元化ファイル

dsc: 内部での説明・エラーログ出力用
msgEN: ユーザ表示用(英語)
msgJA: ユーザ表示用(日本語)

・コード内でエラー表示をする場合は、このファイルに採番・追記してから、エラー処理することを想定
・発生元ごとにコード番号を増やす
・バッチ系は管理しなくてもよいと思われるが、番号体系は予約
*/

$errorMessage = array (

	//defaultは全て要素が入っている必要あり
    'default' => array(
		'dsc' => "Unspecified Error Code.",
		'msgEN' => "System Error.",
		'msgJA' => "システムエラーが発生しました。",
	),
	
	//entryform.php
	'101' => array(
		'dsc' => "Invalid characters are entered in Name box.",
		'msgEN' => "Invalid characters are entered in Name box.",
		'msgJA' => "名前欄の値を入れなおしてください。",
	),		
	'102' => array(
		'dsc' => "Invalid characters are entered in EMAIL box.",
		'msgEN' => "Invalid characters are entered in EMAIL box.",
	),		
	'103' => array(
		'dsc' => "Fail to Authorize. Please confirm your ID/PWD.",
		'msgEN' => "Fail to Authorize. Please confirm your ID/PWD.",
	),	
	'104' => array(	//7
		'dsc' => "Invalid Session cokkie",
		'msgEN' => "Invalid Session cokkie",
	),	
	'105' => array(	//10
		'dsc' => "Invalid E-mail address with regular explession",
		'msgEN' => "Invalid E-mail Address is found. Please Check your entried e-mail address",
	),		
	'106' => array(	//11
		'dsc' => "Invalid E-mail address with RFC violation(hoge@var@foo.com",
		'msgEN' => "Invalid E-mail Address is found. Please Check your entried e-mail address",
	),	
	'107' => array(	//12
		'dsc' => "Invalid E-mail address with RFC violation(DNS Recode check ERR)",
		'msgEN' => "Invalid E-mail Address is found. Please Check your entried e-mail address",
	),	
	'108' => array(	//13
		'dsc' => "Fail to send E-mail.",
	),	

	//register.php
	'201' => array(	//4
		'dsc' => "Session key has been expired. Re-entry is needed.",
		'msgEN' => "Session key has been expired. Re-entry is needed.",
	),		
	'202' => array(	//5
		'dsc' => "No matched session key. Invalided session key is ?",
		'msgEN' => "No matched session key. Invalided session key is ?",
	),		
	'203' => array(	//6
		'dsc' => "This session key is invalided(Length is invalid). Please contact with G-cluster Content Support Team.",
		'msgEN' => "This session key is invalided(Length is invalid). Please contact with G-cluster Content Support Team.",
	),

	//uploadform.php
	'301' => array(
		'dsc' => "Error here.",
		'msgEN' => "Error here.",
	),
	
	//upload.php
	'401' => array(
		'dsc' => "Error here.",
		'msgEN' => "Error here.",
	),
	
	//launch/index.php
	'501' => array(
		'dsc' => "Unsupported User-Agent.",
		'msgEN' => "Your device is not supported in Cloud Test.",
	),		
	'502' => array(
		'dsc' => "got accessKey parameter, but not in develper.csv --> illegal access? ",
		'msgEN' => "Invalid Access.",
	),		
	'503' => array(
		'dsc' => "no accessKey parameter and no session",
		'msgEN' => "Parameter error. accessKey is required.",
	),	
	'504' => array(
		'dsc' => "cannot open developer csv",
		'msgEN' => "System Error.",
	),	
	'505' => array(
		'dsc' => "cannot open scheduled csv",
		'msgEN' => "System Error.",
	),
	
	//launch/startGame.php
	'601' => array(
		'dsc' => "cannot get HHID and gameName",
		'msgEN' => "Invalid Parameters.",
	),		
	'602' => array(
		'dsc' => "illegal User-Agent other than GC client (direct Browser Access??) ",
		'msgEN' => "Illegal Access. Recorded.",	
	),		
	'603' => array(
		'dsc' => "timestamp invalid.",
		'msgEN' => "Illegal Access. Recorded.",	
	),	
	'604' => array(
		'dsc' => "invalid hsv (illegal access??)",
		'msgEN' => "Illegal Access. Recorded.",	
	),	
	'605' => array(
		'dsc' => "Curl initialization failed.",
		'msgEN' => "System Error.",
	),
	'606' => array(
		'dsc' => "GC Standard dispatcher Request call failed.",
		'msgEN' => "System Error.",
	),
	'607' => array(
		'dsc' => "launchPortal() call failed.",
		'msgEN' => "System Error.",
	),
	'608' => array(
		'dsc' => "GC Standard dispatcher return parameter is null.",
		'msgEN' => "System Error.",
	),
	'609' => array(
		'dsc' => "GC Standard dispatcher return parameter is invalid.",
		'msgEN' => "System Error.",
	),
	'610' => array(
		'dsc' => "GC Standard dispatcher returned not-listed error code.",
		'msgEN' => "System Error.",
	),	
	//GCの慣習にあわせて残すが、600番台をつけても問題ないはず
	'17' => array(
		'dsc' => "GC Standard dispatcher returned -525. (Capacity Full)",
		'msgEN' => "Capacity Full. Please access several minutes later again. ",
	),
	'52' => array(
		'dsc' => "GC Standard dispatcher returned -529. (??)",
		'msgEN' => "System Error.",
	),		

	//preprocess.php
	'2001' => array(
		'dsc' => "Reserved.",
	),

	//scheduling.php
	'2101' => array(
		'dsc' => "Reserved.",
	),

	//start.php
	'2201' => array(
		'dsc' => "Reserved.",
	),

	//stop.php
	'2301' => array(
		'dsc' => "Reserved.",
	),
);
				
?>

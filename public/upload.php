<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>upload result</title>
</head>

<body>
<pre>

<?php

include_once("../config.php");
include_once("../common.php");

////////////////////////////////////////////////
WriteLog("IFO::Start get lock process");
////////////////////////////////////////////////
if(lock_process("") == false){
    WriteLog("WRN::Unlock process with error exit");
    exit(1);
}
////////////////////////////////////////////////



setlocale(LC_ALL, 'ja_JP.UTF-8');

// initialize
$developer_id = "";
$developer_password = "";
$developer_mail = "";
$content_id = "";
$rendering_type = "";
$developer_ipaddress ="";
$developer_accesskey ="";

$upload_password = "";
$proceed_flag = false;



session_start();
if (isset($_SESSION['uploadKey']) && isset($_SESSION['developer_mail']) ){
	WriteLog("DEBUG::SET _SESSION");
	$upload_password = $_SESSION['uploadKey'];
	//$upload_password = $_POST['upload_password']; //<-こっちを使う
	$developer_mail = $_SESSION['developer_mail'];

	WriteLog($_SESSION['uploadKey']);
	Writelog($_SESSION['developer_mail']);
	Writelog($upload_password);
	Writelog($developer_mail);

	//entry.csvの読み込み
	if(isset($upload_password) && isset($developer_mail)){
		WriteLog("DEBUG::Read entrt.csv");
		$handle = fopen($entry_csv, "r");
		if(flock($handle, LOCK_EX)){
			while (($line = fgetcsv($handle)) !== false){
				$data[] = $line;
			}
		}
	} else {
		WriteLog("DEBUG::CANNOT Read Entry.csv!");
	}

	flock($handle, LOCK_UN);
	fclose($handle);

	$numCountDatas = count($data);

	for ($p = $numCountDatas; $p > 0; $p--){ //最も新しくアップロードされたところから探す
		if($p !== 0){
			if(strpos($developer_mail, $data[$p][7]) !== false){ //入力されたmailAddressとの合致を検索する
				if (strpos($data[$p][0], "1") !== false){		 //Activateされているかどうか
					WriteLog("IFO::($developer_id) is actiavted");
					if(strpos($upload_password, $data[$p][12]) !== false){ //入力されたUploadKeyと合致するか
						WriteLog("IFO::($developer_id) matched with upload key");
						$proceed_flag = true;
						$developer_id = $data[$p][2];	//entry時に生成したdeveloper_idを渡す
						WriteLog("IFO::(" . $developer_id . ")Upload process starts.");
						break;
					} else {
						$proceed_flag = false;
						WriteLog("DEBUG::No.$p is not matched with uploadKey aaa");
					}
				} else {
					$proceed_flag = false;
					WriteLog("DEBUG::No.$p is non activated bbb");
				}
			} else {
				$proceed_flag = false;
				WriteLog("DEBUG::No.$p is unmatched mail address ccc");
			}
		} else {
			WriteLog("DEBUG:: asdasd");
		}
	}
} else {
	if (isset($_GET['accessKey'])){
		WriteLog("DEBUG::SET accessKey");
		$developer_accesskey = $_GET['accessKey'];

			//developer.csvの読み込み
		if(isset($developer_accesskey)){
			$dev_handle = fopen($developer_csv, "r");
			if(flock($dev_handle, LOCK_EX)){
				while (($line = fgetcsv($dev_handle)) !== false){
					$data[] = $line;
				}
			}
		}
		flock($dev_handle, LOCK_UN);
		fclose($dev_handle);

		$numCountDatas = count($data);

		for ($p = $numCountDatas; $p > 0; $p--){
			if($p !== 0){
				if(strpos($developer_accesskey, $data[$p][6]) !== false){ //accessKeyと合致するか
					$proceed_flag = true;
					$developer_id = $data[$p][0];
					$developer_password = $data[$p][1];
					$developer_mail = $data[$p][2];
					$developer_ipaddress = $data[$p][5];
					$created_at = $data[$p][7];

					//session_start();
					//$_SESSION['uploadKey'] = $upload_password;
					//$_SESSION['developer_mail'] = $developer_mail;

					WriteLog("IFO::(". $developer_id .") Upload process starts");
					WriteLog("DEBUG::the number is => $p");

					break;
				} else {
					$proceed_flag = false;
				}
			}
		}
	} else {
		/*
		$code = 7;
		//header("Location: https://test.gcluster.jp/gctest/ykandori_test/CloudTest/error.php?errorcode=$code");
		header("Location: https://test.gcluster.jp/gctest/ykandori_test/CloudTest/uploadform.php?errorcode=$code");
		WriteLog("ERR::Upload_password is not set");
		exit();
		*/
		unlock_process("");
		ErrorCodeCallBack(8, true);
	}
}


if(!$proceed_flag){
	/*
	$code = 3;
	//header("Location: https://test.gcluster.jp/gctest/ykandori_test/CloudTest/error.php?errorcode=$code");
	header("Location: https://test.gcluster.jp/gctest/ykandori_test/CloudTest/uploadform.php?errorcode=$code");
	WriteLog("ERR::Fail to auth because some unspecified reasons");
	exit();
	*/
	unlock_process("");
	ErrorCodeCallBack(9, true);

}


							//2016/11/24 Y.Okamoto
							//2016/12/19 Y.Okamoto




////////////////////////////////////////

$ok_flag = false;
// data file upload check
static $mimetype_option = array('application/zip', 'application/x-zip-compressed');

if (isset($_FILES['userfile'])) {	//check illegal parameter
	if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {

		//check extension and mimetype
		$ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
		$type = $_FILES['userfile']['type'];
		$filesize = $_FILES['userfile']['size'];
		if ( ($ext === 'zip') && (in_array($type, $mimetype_option, true)) ) { //check Extention "zip" or not
			if( $filesize <= 2147483648 ){ //check file size. if over 2GB(2147483648byte), it should be error.
				$ok_flag = true;
				WriteLog("IFO::(". $developer_id . ")Upload file size is $filesize byte");
				WriteLog("IFO::(" . $developer_id . ")Pass to check upload file");
			} else { //file size is too big.
				echo "File size is too big! ($filesize byte) \n";
				WriteLog("ERR::($developer_id)Upload file size is too big!! This is $filesize byte");
				unlock_process("");
				ErrorCodeCallBack(14, true);
			}
		} else {	//is not zip?
			echo "Please upload zip file! \n";
			WriteLog("ERR::(" . $developer_id . ")Uploaded file is NOT ZIP!! Please confirm what file type is.");
		}
	} else {	// failure in upload
		echo "Please select zip File !\n";
		WriteLog("ERR::(" . $developer_id . ")Fail to upload user content!!");

		// Error Handling ToDo
		// - file size is bigger than MAX_FILE_SIZE
		// - file size is bigger than post_max_size, upload_max_file_size etc.
		// - upload not complete, no file, cannot move, permission error 
		// - not zip file (other type file)
	}
}

echo '<br>';
echo 'debug info:';
print_r($_FILES);

	// Display ToDo
	// - File Size
	// - Date & Time
	// - Inquiry No.

	// Log ToDo
	// (In addition to above)
	// -  original file name
	// -  stored file name
	

// rendering type from input
if(isset($_POST['rendering_type'])) {
	$rendering_type = $_POST['rendering_type'];
	if (in_array($rendering_type, $rendering_type_option, true)) {
		$ok_flag = true;
		echo $rendering_type;
		echo '<br>';
	} else {
		echo "Illegal Rendering Type !\n";
	}
} else {
	echo "Please select Rendering Type !\n";
}


// if input is OK
if ($ok_flag) {
		WriteLog("ok_flag is greenLight");
	// game data zip file store	
	
	// use timestamp as Content ID and filename
	// for easy handling in csv and as file name(no space and not unix time) , use simple XMLRPC time format
	// http://php.net/manual/ja/datetime.formats.compound.php
	
	$content_id = date('Ymd\tHis');
	$uploadfile = $upload_dir . $content_id . ".zip";		
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		echo "successfully uploaded.\n";
		print "Stored To:" . $uploadfile;	
	} else {
		echo "Error in File upload!\n";
	}
	
	/* register.newに移したためコメントアウト
	//generate password for following process (start page access, AWS direct file access, control panel etc.)
	//英小文字、数字のみ。8文字   てきとう
	$developer_password = substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, 8);
	$developer_accesskey = substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, 40);
	*/

	/*
	//other parameters
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {  // for Load Blancer
		$developer_ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$developer_ipaddress = $_SERVER["REMOTE_ADDR"];
	}
	$created_at = date("Y-m-d H:i:sP");
	*/
	
	
	// add to developer list csv
	if (!is_file($developer_csv)) {	// if no file, initialize
		$dev_handle2 = fopen($developer_csv, "w");
		if ($dev_handle2) {
			fputcsv($dev_handle2, $csv_header);
		}
		fclose($dev_handle2);
	}
	
	if(($rhandle = fopen($developer_csv, "r")) === false){
		WriteLog("ERR::Not exist 'developer.csv'");
	} else {
		WriteLog("IFO::Success to read 'developer.csv'");
	}

	if(flock($rhandle, LOCK_EX)){
		WriteLog("IFO::File lock to read developer.csv");
		while (($rline = fgetcsv($rhandle)) !== false){
			$dev_data[] = $rline;
		}
	} else {
		WriteLog("ERR::Fail to open csv");
	}

	if(fclose($rhandle)){
		WriteLog("IFO::Success to close and unlock csv file");
	} else {
		WriteLog("");
	}

	$numCountDeveloperDatas = count($dev_data);


	for ($i = 0; $i < $numCountDeveloperDatas; $i++){

		if($i == 0){
			$wwData[$i] = $dev_data[$i];
		}

		if($i != 0){
			WriteLog("IFO::Target DeveloperID is $developer_id" . ". its type is " . gettype($developer_id));
			WriteLog("IFO::serch target is " . $dev_data[$i][0] . ". its type is " . gettype($dev_data[$i][0]));
			
			if(strcmp($dev_data[$i][0], $developer_id)  !== 0){
				WriteLog("No.$i is not matched (" . $dev_data[$i][0] . ")");
				$wwData[$i] = array(
					$dev_data[$i][0],
					$dev_data[$i][1],
					$dev_data[$i][2],
					$dev_data[$i][3],
					$dev_data[$i][4],
					$dev_data[$i][5],
					$dev_data[$i][6],
					$dev_data[$i][7],
					null,
					null
				);
			} else {
				WriteLog("No.$i is matched");
				WriteLog("$developer_id, $developer_password, $developer_mail, $content_id, $rendering_type");
				$wwData[$i] = array(
					$developer_id,
					$dev_data[$i][1],
					$developer_mail,
					$content_id,
					$rendering_type,
					$dev_data[$i][5],
					$dev_data[$i][6],
					$dev_data[$i][7],
					null,
					null
				);

				print_r($wwData[$i]);
			}
		}
	}

	$wwhandle = fopen($developer_csv, "w");
	if(flock($wwhandle, LOCK_EX)){
		foreach ($wwData as $var){
			fputcsv($wwhandle, $var);
		}
	}
	flock($wwhandle, LOCK_UN);
	fclose($wwhandle);


	/*
			$handle = fopen($developer_csv, "a");	// if file exists, append line
			$array = array($developer_id, $developer_password, $developer_mail, $content_id, $rendering_type, $developer_ipaddress, $developer_accesskey, $created_at, NULL, NULL);
			if ($handle) {	
				fputcsv($handle, $array);
			}
			fclose($handle);
	*/
} else {
	WriteLog("ok_flag is not OK...");
}

unlock_process("");
exit(0);

?>

</pre>
<button type="button" onclick="history.back()">Back</button>
</body>
</html>

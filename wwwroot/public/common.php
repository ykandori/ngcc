<?php

include_once(dirname(__FILE__) . "/errorcode.php");

function WriteLog($output){
	global $log_dir;
	
	$name=basename($_SERVER['PHP_SELF'], '.php');
	$flog = fopen($log_dir.$name.'-'.date("Ymd").".log" ,"a");
	fwrite($flog,date("Y-m-d H:i:s, "));
	fwrite($flog,$output."\r\n");
	fclose($flog);
}

//エラーページの表示
//$errorCode: エラーコード(errorcode.phpに定義)
//$infoString: ログに追記する情報
//
//・日本語メッセージがあればそれを優先
//・同一ディレクトリのerror_page.phpを読み込み表示
function show_error_page($errorCode,$infoString){
	global $errorMessage;

	if ($errorMessage[$errorCode] == NULL){
		$msg = $errorMessage['default']['msgJA'];
	}else{
		if($errorMessage[$errorCode]['msgJA'] == NULL){
			$msg = $errorMessage[$errorCode]['msgEN'];
		}else{
			$msg = $errorMessage[$errorCode]['msgJA'];
		}
	}

	WriteLog("Error Page: (".$errorCode.") ".$errorMessage[$errorCode]['dsc']." , ".$infoString);
	if(is_file("./error_page.php")){
		include("./error_page.php");
	}
	exit(1);
}

//エラーコードによるログ出力
//エラーコードでログを記録したい場合に使用(本当にいる？)
function log_error_by_code($errorCode,$infoString){
	global $errorMessage;

	if ($errorMessage[$errorCode] == NULL){
		$log = $errorMessage['default']['dsc'];
	}else{
		if($errorMessage[$errorCode]['dsc'] != NULL){
			$log = $errorMessage['default']['dsc'];
		}else{
			$msg = $errorMessage[$errorCode]['dsc'];
		}
	}
	WriteLog("Error Page: (".$errorCode.") ".$errorMessage[$errorCode]['dsc']." , ".$infoString);
}



// プロセスの多重起動防止ロック取得・ロック解除
// 引数: 指定するとその文字列、指定しないとPHPファイル名、のロックファイルを作成・削除する(ロックファイルの場所は、$log_dir。ロックファイルの中身はPID)
// 戻り値: true:OK false:NG
// (ファイル削除失敗等で)12時間以上経過したロックは無視する
function lock_process($lockname) {	
	global $log_dir;

	if ( empty($lockname) ){
		$lockfile = $log_dir . basename($_SERVER['PHP_SELF'], '.php') . ".lock";	
	}else{
		$lockfile = $log_dir . $lockname . ".lock";	
	}
	
	if ( !file_exists($lockfile) || (filemtime($lockfile) < strtotime('-12 hour')) ){	//no lock file or old file
		$handle = fopen($lockfile ,"w");
		fwrite($handle, getmypid());
		fclose($handle);
		return true;
	}else{	//lock file exists and enough new
		WriteLog("lock file exists: " . $lockfile);
		return false;
	}
}

function unlock_process($lockname) {	
	global $log_dir;

	if ( empty($lockname) ){
		$lockfile = $log_dir . basename($_SERVER['PHP_SELF'], '.php') . ".lock";	
	}else{
		$lockfile = $log_dir . $lockname . ".lock";	
	}
	
	if ( file_exists($lockfile) ){
		if (unlink($lockfile)){
			return true;
		}else{
			WriteLog("remove lock file failed!: " . $lockfile);
			return false;
		}
	}
}


// ディレクトリ階層以下のコピー
// 引数: コピー元ディレクトリ、コピー先ディレクトリ
// 戻り値: 結果 true:OK false:NG
function dir_copy($dir_name, $new_dir) {
		
	if (!is_dir($new_dir)) {
		mkdir($new_dir);
	}

	if (is_dir($dir_name)) {
		if ($dh = opendir($dir_name)) {
			while (($file = readdir($dh)) !== false) {
				if ($file == "." || $file == "..") {
					continue;
				}
				if (is_dir($dir_name . "/" . $file)) {
					if (!dir_copy($dir_name . "/" . $file, $new_dir . "/" . $file) ) {
						return false;
					}
				} else {
					if (!copy($dir_name . "/" . $file, $new_dir . "/" . $file) ) {
						return false;
					}
				}
			}
			closedir($dh);
		}
	}
	return true;
}
	
// ディレクトリ階層以下の削除
// 引数: 削除対象ディレクトリ
// 戻り値: 結果 true:OK false:NG
function dir_remove($dir_name) {

	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($dir_name, FilesystemIterator::SKIP_DOTS),
		RecursiveIteratorIterator::CHILD_FIRST);

	foreach ( $files as $file ) {
		if ( $file->isDir() === true ) {
			if (!rmdir($file->getPathname()) ) {
				return false;
			}	
		} else {
			if (!unlink($file->getPathname()) ) {
				return false;
			}
		}
	}

	rmdir($dir_name);
		
	return true;
}


function get_AWS_instance_status($instanceId, $region) {
	
	//最初から、下記コマンドで出力を絞ってもよいが、ここでは、柔軟性のために、プログラムで解析するようにした
	//aws ec2 describe-instances --instance-id i-4363a7cd --region ap-northeast-1 --query "Reservations[].Instances[].State[].[Name]" --output=text
	//AWS CLIが出力するJSONの構造は、下記コマンドで見てください。
	//aws ec2 describe-instances --instance-id i-4363a7cd --region ap-northeast-1

	//get Instance Information by AWS CLI
	$command = 'aws ec2 describe-instances --instance-id ' . $instanceId . ' --region ' . $region ;
	//WriteLog($command);
	exec($command, $out, $ret);
	//print_r($out);
	
	//Interpret JSON (execの結果は、改行ごとに配列になるので、implodeで一つの変数に入れる)
	$json_out = json_decode(implode("\n", $out));
	//var_dump($json_out);

	if ($json_out == NULL){
		WriteLog("command return error: \n" . implode("\n", $out) );
		return false;
	}else{
		$status = $json_out->Reservations[0]->Instances[0]->State->Name;
		if ($status == NULL){
			WriteLog("command return error: \n" . implode("\n", $out) );
			return false;
		}else{
			return $status;	
			
			//文字列の方を返却
			// 0 : pending
			// 16 : running
			// 32 : shutting-down
			// 48 : terminated
			// 64 : stopping
			// 80 : stopped
		}		
	}
	
}

function start_AWS() {
	global $aws_tm_instance_id, $aws_gs_instance_id, $aws_region;
	
	//get status
	$tm_status = get_AWS_instance_status($aws_tm_instance_id, $aws_region);
	$gs_status = get_AWS_instance_status($aws_gs_instance_id, $aws_region);
	//WriteLog("GS Status: " . $gs_status . " TM Status: " . $tm_status);
	

	//TM start
	if($tm_status === "stopped"){		
		$command = 'aws ec2 start-instances --instance-id ' . $aws_tm_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $out2, $ret);
		print_r($out2);
		if($ret != 0){
			WriteLog("AWS command failed.");
			return false;
		}
	}elseif($tm_status === "running"){
		WriteLog("AWS TM is already running");
	}else{
		WriteLog("AWS TM is other status");
		return false;
	}
	//GS start
	if($gs_status === "stopped"){
		$command = 'aws ec2 start-instances --instance-id ' . $aws_gs_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $out1, $ret);
		print_r($out1);
		if($ret != 0){
			WriteLog("AWS command failed.");
			return false;
		}
	}elseif($gs_status === "running"){
		WriteLog("AWS GS is already running");
	}else{
		WriteLog("AWS GS is other status");
		return false;
	}
			

	//runningになるまで、待つ
	//・"instance-running"だとネットワークはまだ起動していないが、"instance-status-ok"だと時間がかかりすぎるため、前者を使用
	//・時間がかかるので、両方ともstartをかけたあとでまとめてwaitする
	//・TMの方が早いので、先に実行

	//TM start wait
	if($tm_status === "stopped"){
		$command = 'aws ec2 wait instance-running --instance-ids ' . $aws_tm_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $output, $ret);
		WriteLog("AWS TM is running status");
	}	
	//GS start wait
	if($gs_status === "stopped"){
		$command = 'aws ec2 wait instance-running --instance-ids ' . $aws_gs_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $output, $ret);
		WriteLog("AWS GS is running status");
	}
	
	return true;
}

function stop_AWS() {
	global $aws_tm_instance_id, $aws_gs_instance_id, $aws_region;
	
	//get status
	$tm_status = get_AWS_instance_status($aws_tm_instance_id, $aws_region);
	$gs_status = get_AWS_instance_status($aws_gs_instance_id, $aws_region);
	//WriteLog("GS Status: " . $gs_status . " TM Status: " . $tm_status);	

	
	//TM stop
	if($tm_status === "running"){		
		$command = 'aws ec2 stop-instances --instance-id ' . $aws_tm_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $out2, $ret);
		print_r($out2);
		if($ret != 0){
			WriteLog("AWS command failed.");
			return false;
		}
	}elseif($tm_status === "stopped"){
		WriteLog("AWS TM is already stopped");
	}else{
		WriteLog("AWS TM is other status");
		return false;
	}
	//GS stop
	if($gs_status === "running"){
		$command = 'aws ec2 stop-instances --instance-id ' . $aws_gs_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $out1, $ret);
		print_r($out1);
		if($ret != 0){
			WriteLog("AWS command failed.");
			return false;
		}
	}elseif($gs_status === "stopped"){
		WriteLog("AWS GS is already stopped");
	}else{
		WriteLog("AWS GS is other status");
		return false;
	}
	

	//TM stop wait
	if($tm_status === "running"){
		$command = 'aws ec2 wait instance-stopped --instance-ids ' . $aws_tm_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $output, $ret);
		WriteLog("AWS TM is stopped status");
	}
	//GS stop wait
	if($gs_status === "running"){
		$command = 'aws ec2 wait instance-stopped --instance-ids ' . $aws_gs_instance_id . ' --region ' . $aws_region ;
		WriteLog($command);
		exec($command, $output, $ret);
		WriteLog("AWS GS is stopped status");
	}
	
	return true;
}


function mail_notice_to_admin($message) {
	global $script_dir;
		
	$content = $message;

	$from = "gcadmin@support.gcluster.com";
	$subject = "Cloud Test Notice";
	$to = "gcadmin@support.gcluster.com";
	$to = "ykandori@broadmediagc.co.jp";	//test
	$header = "From:" . $from . "\n";
		
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	mb_send_mail($to, $subject, $content, $header);
	WriteLog("Admin notice mail sent to " . $to);
	
	return true;
}
	

	
//For Public PHPs

//  受信パラメータのログ出力
function paramLog(){
	if($_SERVER['REQUEST_METHOD']=="GET"){
		$req = $_SERVER['QUERY_STRING'];
	}else{
		$ks = array_keys($_POST);
		$req = "";
		foreach ($ks as $key){
			if($req===""){
				$req = $key."=".$_POST[$key];
			}else{
				$req = $req."&".$key."=".$_POST[$key];
			}
		}
	}
	WriteLog(basename($_SERVER['PHP_SELF']) . " Query String: {$req}");
}	
	

function timestampCheck(){
	if(isset($_GET['timestamp'])){
		$timestamp = $_GET['timestamp'];
	}elseif(isset($_POST['timestamp'])){
		$timestamp = $_POST['timestamp'];
	}else{
		WriteLog("timestamp is not found.");
		errorResponse($clientId, 212, "timestamp is required.",$outputType);	//パラメータがないとエラーにする場合
		//$timestamp = time();													//パラメータなくても通す場合
	}
	$now = time();
	if( ($timestamp < $now-300) || ($now+300 < $timestamp)){	//5分以上ずれていた場合
		//errorResponse($clientId, 213, "timestamp is invalid.",$outputType);
		WriteLog("timestamp is invalid. your timestamp: $timestamp now: $now.");
		return false;
	}
	return true;
}

$secret_key = "QTdn9K98mFD9Gqzw";

function hsvCheck(){
	global $secret_key;
	
	if($_SERVER['REQUEST_METHOD']=="GET"){
		$req = $_SERVER['QUERY_STRING'];
	}else{
		$keys = array_keys($_POST);
		$req = "";
		foreach ($keys as $key){
			if($req===""){
				$req = $key."=".$_POST[$key];
			}else{
				$req = $req."&".$key."=".$_POST[$key];
			}
		}
	}

	if (preg_match('/^(.+)\&hsv\=(.+)$/',$req,$matches) == 1){
		//WriteLog("query: {$matches[1]}");
		//WriteLog("hsv param: {$matches[2]}");
		if($_SERVER['REQUEST_METHOD']=="GET"){
			$user_hsv = $matches[2];
		}else{
			$user_hsv = rfc3986_urlencode($matches[2]);
		}
		//WriteLog("user_hsv: {$user_hsv}");
		$calc_hsv = rfc3986_urlencode(base64_encode(hash_hmac('sha256', $matches[1], $secret_key, true)));
		if($calc_hsv === $user_hsv){
			WriteLog("hsv is matched: $calc_hsv");
			return true;
		}else{
			//errorResponse($clientId, 211, "parameter error hsv is invalid.",$outputType);
			WriteLog("hsv is not matched: $calc_hsv");
			return false;
		}
    }else{
		//errorResponse($clientId, 210, "parameter error hsv is required.",$outputType);
		WriteLog("hsv is not found");
		return false;
	}
}

function rfc3986_urlencode($str){
	return str_replace('%7E', '~', rawurlencode($str));
}


?>

<?php
	
	date_default_timezone_set('Asia/Tokyo');

	//////////////////////////////////
	// Directory Settings
	//////////////////////////////////
	
	switch(php_uname("n")){
		case  'BBTW1ASIT02':	//192.168.50.37 / Linux / PHP5.1.6
			$base_dir = '/mnt/NAS/NASGROUP01/BBTW1NAS01/test2/game/CloudTest/';
			$log_dir = $base_dir . 'Data/log/Public/';
			break;
		case  'BBTW1DEP02':		//192.168.50.109 / Windows / PHP5.3.3
			$base_dir = '\\\\192.168.50.61/test2/game/CloudTest/';
			$log_dir = $base_dir . 'Data/log/';
			break;
		default:
			$base_dir = '\\\\192.168.50.61/test2/game/CloudTest/';
			$log_dir = $base_dir . 'Data/log/';
	}
	
	$script_dir = $base_dir . 'php/';
	$upload_dir = $base_dir . 'Data/upload/';
	$extract_dir = $base_dir . 'Data/extract/';
	$scheduled_dir = $base_dir . 'Data/scheduled/';
	$backup_dir = $base_dir . 'Data/backup/';
	$scheduling_dir = $base_dir . 'Data/scheduling/';

	$entry_csv = $scheduling_dir .'entry.csv';
	$developer_csv = $scheduling_dir . 'developer.csv';
	$scheduled_csv = $scheduling_dir . 'scheduled.csv';
	
	$win_defender_path = "C:/Program Files/Microsoft Security Client/";
	
	/*
	// for migration old setting Linux 50.37
	$base_dir_linux = '/mnt/NAS/NASGROUP01/BBTW1NAS01/test2/game/CloudTest/';
	$upload_dir_linux = $base_dir_linux . 'Data/upload/';
	$scheduling_dir_linux = $base_dir_linux . 'Data/scheduling/';
	$entry_dir_linux = $base_dir_linux . 'Data/entry/';
	$entry_csv_linux = $entry_dir_linux .'entry.csv';
	*/
	
	//////////////////////////////////
	// Parameter Settings
	//////////////////////////////////
	$entry_csv_header = array("Activation", "session_key", "developer_id", "Family_name", "First_name","Family_name_fonetic", "First_name_phonetic","Email_Address", "Company_name", "Job_category", "Motivation", "Entried_at", "Upload_key");
	$csv_header = array( "developer_id", "password", "mail", "content_id", "rendering_type", "IPAddress", "access_key", "created_at", "test_start_at", "test_end_at" );
	$rendering_type_option = array('DirectX8','DirectX9','DirectX11','OpenGL');
	$prohibited_exe_name = array( "delete", "uninstall", "config", "setup", "report");
	
	//////////////////////////////////
	// GC Server Settings
	//////////////////////////////////	
	
	$sch_dir = '\\\\192.168.50.9/hila/Games/CloudTestGames/';
	$sch_url = 'http://192.168.50.9:15800';
	$sch_backend = '192.168.50.55';
	
	//////////////////////////////////
	// AWS Settings
	//////////////////////////////////
	
	$aws_gs_instance_id = 'i-4363a7cd';
	$aws_tm_instance_id = 'i-2406c9aa';
	$aws_region = 'ap-northeast-1';
	
	$gs_ip = '10.1.7.1';
	$gs_dir = '\\\\' . $gs_ip . '/hila/Games/CloudTestGames/';
	
	$htpasswd_path = 'C:/hila/runenv2/Apache2.2/bin';
	$apache_passwd_dir = '\\\\' . $gs_ip . '/hila/runenv2/Apache2.2/conf/';
	
	//for DNS64
	$tm_hosttable = array (
		"202.183.83.119" => "tm1.gcluster.jp",
		"202.183.83.120" => "tm2.gcluster.jp",
		"202.183.83.98"  => "tmt1.gcluster.jp",
		"202.183.83.99"  => "tmt2.gcluster.jp",
		"202.183.83.84"  => "tmt3.gcluster.jp",
		"202.183.83.85"  => "tmt4.gcluster.jp",
		"202.183.83.216" => "tmcpdev1.gcluster.jp",
		"202.183.83.217" => "tmcpdev2.gcluster.jp",
		"54.64.250.19" => "tmcloudtest1.g-cluster.jp",
		);
?>

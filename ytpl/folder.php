<?php
error_reporting(0);
$time_start = microtime( true );

$API_READ_TOKEN='ica_Q91p0c-BoCFcCt0M58RLP_YD5vyuSf_qiIhagXKM36A7dzegnQ..';
$API_WRITE_TOKEN= '3w6N6ouRt39sFKgIBOTw98DQjwEgmD6c9RsvSbiSbESiZjiDHHzJXw..';
$CLIENT_ID     = '915aacea-2c2c-4135-9148-de5580f7388a';
$CLIENT_SECRET = 'H06Ba8MOdID1DpZM7rq1s_0ETv2XrTK82c2mZw8TO7P3mrR7cPyc8MqYrvi47ujWviKim57sq3ayNbD9FmizbA';
$ACCOUNT_ID= '4468173350001';


require('bc-mapi.php');

$bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN, $CLIENT_ID, $CLIENT_SECRET, $ACCOUNT_ID);

$bc->__set('secure',TRUE);


try {
  $access_token = $bc->get_access_token();
  //echo '<pre>';
  //print_r($access_token);
  //echo '</pre>';
} catch(Exception $error) {
  echo '<pre>';
  print_r($error);
  echo '</pre>';
}


try {
  $folder_list = $bc->get_folder_list($access_token);
} catch(Exception $error) {
  echo '<pre>';
  print_r($error);
  echo '</pre>';
}

$folders = json_decode($folder_list, TRUE);


foreach ($folders as $key => $value) {
	echo '<h1>Folder Details</h1><hr>';
	echo 'Name : '.$value['name'].'<br>';
	echo 'Video Count : '.$value['video_count'].'<br>';
	echo 'ID : '.$value['id'].'<br>';

	if($value['video_count'] > 0){
		try {
		  $video_list = $bc->get_folder_list($access_token, 'GET', 'videos', $value['id']);
		  echo '-----------Related Video(s)-----------<br><br>';
		  $videos = json_decode($video_list, TRUE);
		  foreach ($videos as $k => $v) {
		  	echo 'Name : '.$v['name'].'<br>';
		  	echo 'ID : '.$v['name'].'<br><br>';
		  }
		  echo '--------------------------------------<br>';
		} catch(Exception $error) {
		  echo '<pre>';
		  print_r($error);
		  echo '</pre>';
		}
	}
	echo '<br><hr><br>';
}


echo '<br> Render Time : '.number_format( microtime( true ) - $time_start, 10 );
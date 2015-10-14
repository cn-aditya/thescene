<?php
error_reporting(0);
require_once realpath(dirname(__FILE__) . '/conf.php');




$folder_id = '56024c44e4b0dac7c5a6ba18';

$bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN, $CLIENT_ID, $CLIENT_SECRET, $ACCOUNT_ID);
$link = new mysqli($host_name, $user, $password, $db_name);

$bc->__set('secure',TRUE);


try {
  $access_token = $bc->get_access_token();
} catch(Exception $error) {
  echo '<pre>';
  print_r($error);
  echo '</pre>';
}

$qry = "SELECT yt_id AS youtube, bc_id AS brightcove FROM ts_yt_bc_sync_ref_ids WHERE failed_yt_id = '0' AND folder_move = '0'";


	if ($result = $link->query( $qry )) { //MYSQLI_USE_RESULT

	    //printf("Select returned %d rows.\n", $result->num_rows);

	    if (!$link->query("SET @a:='this will not work'")) {
	        printf("Error: %s\n", $link->error);
	    }

	    if($result->num_rows > 0){
	    	while($obj = $result->fetch_object()){ 

	    		$video_id = $obj->brightcove;

	    		try {
				  $folder_add = $bc->add_video_to_folder($access_token, 'PUT', $folder_id, $video_id);
				  //echo '<pre>';print_r($folder_add);
				  $link->query( "UPDATE ts_yt_bc_sync_ref_ids SET folder_move = '1' WHERE bc_id = '{$video_id}' " );
				} catch(Exception $error) {

				}

		    }
	    }  

	    $result->close();
	}


/*try {
  $folder_id = '5602698ae4b0dac7c5a6bdba';
  $video_id = '4535246700001';
  $folder_add = $bc->add_video_to_folder($access_token, 'PUT', $folder_id, $video_id);
} catch(Exception $error) {
  echo '<pre>';
  print_r($error);
  echo '</pre>';
  exit;
}*/


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
		  	echo 'ID : '.$v['id'].'<br><br>';
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
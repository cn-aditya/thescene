<?php

$htmlBody = '';

if(isset($_POST['submit'])){

	$url = (isset($_POST['url'])) ? $_POST['url'] : '';

	$rand = rand();

	$playlistName = (isset($_POST['list_name'])) ? $_POST['list_name'] : $rand;
	$rawplaylistName = (isset($_POST['list_name'])) ? $_POST['list_name'] : $rand;
	$folder_id = (isset($_POST['folder_id'])) ? $_POST['folder_id'] : '5602698ae4b0dac7c5a6bdba';
	$folder_name = (isset($_POST['folder_name'])) ? $_POST['folder_name'] : 'test';
	$list_id = (isset($_POST['list_id'])) ? $_POST['list_id'] : '0';


	$basePath = '/var/www/html/ts/local/ytpl/';
	$pathToDownload = 'files/';

	$remDir = str_replace(' ','_',$playlistName).'-'.date('m_d_Y_h_i_s');

	$subDir = $remDir;
	$fullPathToDownload = $basePath . $pathToDownload . $subDir;


	exec('mkdir ' .  $pathToDownload.$subDir . '/' );
	exec('chmod -R 777 ' . $fullPathToDownload . '/' );



	//$cmd = '/usr/local/bin/youtube-dl  --output "' . $fullPathToDownload . '/' . '%(id)s--%(title)s.%(ext)s" '. escapeshellcmd($url) . ' --restrict-filenames';
	$cmd = 'youtube-dl  --output "' . $fullPathToDownload . '/' . '%(id)s--%(title)s.%(ext)s" '. escapeshellcmd($url) . ' --restrict-filenames';

	exec($cmd);



	$videoId = array();
	$files = array();

	if (is_dir($fullPathToDownload)) {
	    if ($dh = opendir($fullPathToDownload)) {
	        while (($file = readdir($dh)) !== false) {
	        	
	        	$dtls = explode('--',$file);

	        	$videoId[] = $dtls[0];
	        	$files[] = $dtls[1];
	        	
	        }
	        closedir($dh);
	    }
	}



	$videoDetails = array_combine($videoId, $files);
	unset($videoDetails['.']);
	unset($videoDetails['..']);



	$playlistDetails->name = $playlistName;
	$playlistDetails->videos = $videoDetails;


	$datadir = $fullPathToDownload ; //main data dir


	exec ('chmod -R 777 ' . $fullPathToDownload . '/' );



	$videoDirUrl = 'http://localhost/ts/local/ytpl/files/' . $subDir . '/';

	$bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN, $CLIENT_ID, $CLIENT_SECRET, $ACCOUNT_ID);


	$refVideoId = array();
	$refErrorvideoKeyYT = array();
	foreach($playlistDetails->videos as $key=>$val){
		if( (($key!='') && ($val!='')) && (($key!='.') && ($val!='.'))  && (($key!='..') && ($val!='..')) ){

			$name = str_replace(substr($val, -4), '', $val);

			$videoId = $key;

			try{

			    $listResponse = $youtube->videos->listVideos("snippet,statistics,contentDetails,status,topicDetails",array('id' => $videoId));

			    
			    if (empty($listResponse)) {
			      $htmlBody .= sprintf('<h3>Can\'t find a video with video id: %s</h3>', $videoId);
			    } else {
			      $video = $listResponse[0];

			      echo '<pre>';
			      print_r($video);
			      echo '</pre>';
			     
			  	}
			} catch (Google_Service_Exception $e) {
			  $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
			      htmlspecialchars($e->getMessage()));
			} catch (Google_Exception $e) {
			  $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
			      htmlspecialchars($e->getMessage()));
			}

			
	 
			$params->name = preg_replace('/[^A-Za-z0-9 _\.]/','',str_replace('_', ' ', $name));
			$params->referenceId = $key;
			$params->shortDescription = ($video->snippet['description'] != '') ? substr($video->snippet['description'],0,230) : substr($val,0,240);
			$params->longDescription = ($video->snippet['description'] != '') ? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $video->snippet['description']) : $val;
			$params->startDate = strtotime($video->snippet['publishedAt']);
			$params->endDate = null;
			$params->tags = (is_array($video->snippet['tags'])) ?  $video->snippet['tags'] : null;

			$params->renditions = array();

			$url = $videoDirUrl . $key . '--' . ($val);

			$params->renditions = array(); 
			$params->renditions[0]->displayName = preg_replace('/[^A-Za-z0-9 _\.]/','',str_replace('_', ' ', $name));
			$params->renditions[0]->referenceId = $key;
			$params->renditions[0]->remoteUrl = $url;
			$params->renditions[0]->videoDuration = '-1' ;
			$params->renditions[0]->videoCodec = 'H264';
			$params->renditions[0]->videoContainer = 'MP4';
			$params->renditions[0]->size = '1024';

			echo '<pre>';print_r($params);echo '</pre>';

			


			
			try {
			  // Upload the video and save the video ID
			  $res = $bc->createMediaViaApi('video', $params);
			  $id = $res->result;
			  $refVideoId[$key] = $id;
			  //$refVideoId[] = $id;
			} catch(Exception $error) {
			  // Handle our error
			  echo '<pre>';
			  print_r($error);
			  echo '</pre>';
				$refErrorvideoKeyYT[] = $key;
			  //die();
			}



		}
	}

echo $htmlBody;
//exit;
	//create playlist
	// Take a comma-separated string of video IDs and explode into an array

	$refVideoIdArr = $refVideoId;
	$refVideoId = implode(',', $refVideoId);
	$videoIds = explode(',', $refVideoId);

	// Create an array of meta data from our form fields
	$metaData = array(
	    'name' => $playlistName,
	    'shortDescription' => $playlistName,
	    'videoIds' => $videoIds,
	    'playlistType' => 'explicit'
	);

	// Create the playlist and save the playlist ID

	// Create a try/catch
	  try {
	    // Upload the video and save the video ID
	    $plid = $bc->createPlaylist('video', $metaData);

	  } catch(Exception $error) {
	  	echo 'Unable to create playlist.';
	    // Handle our error
	    echo '<pre>';
	    echo $error;
	    echo '</pre>';
	    //die();
	  }

	  $playlistDetailsFinal->playlist_id = $plid;
	  $playlistDetailsFinal->success_YT_video_ids = $refVideoIdArr;
	  $playlistDetailsFinal->error_YT_video_ids = $refErrorvideoKeyYT;


	/*$bc->__set('secure',TRUE);

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

	foreach ($refVideoIdArr as $fkey => $fvalue) {
		try {
		  $folder_id = $folder_id;
		  $video_id = $fvalue;
		  $folder_add = $bc->add_video_to_folder($access_token, 'PUT', $folder_id, $video_id);
		} catch(Exception $error) {
		  echo '<pre>';
		  print_r($error);
		  echo '</pre>';
		}
	}*/

			
			

	echo '<pre>';
	print_r($playlistDetailsFinal);
	echo '</pre>';


	$link = new mysqli($host_name, $user, $password, $db_name);

	/* check connection */
	if ($link->connect_errno) {
	    printf("Connect failed: %s\n", $link->connect_error);
	    exit();
	}

	foreach ($refVideoIdArr as $skey => $svalue) {
		$qry = "INSERT INTO ts_yt_bc_sync_ref_ids SET yt_id = '{$skey}' , bc_id = '{$svalue}' , failed_yt_id = '0', folder = '{$folder_name}', yt_pl_id = '{$list_id}' ";
		$link->query($qry);
	}

	foreach ($refErrorvideoKeyYT as $vfkey => $vfvalue) {
		$qry = "INSERT INTO ts_yt_bc_sync_ref_ids SET yt_id = '0' , bc_id = '0' , failed_yt_id = '{$vfvalue}', folder = '{$folder_name}', yt_pl_id = '{$list_id}'  ";
		$link->query($qry);
	}

}

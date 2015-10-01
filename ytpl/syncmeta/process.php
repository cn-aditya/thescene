<?php

$htmlBody = '';

if(isset($_POST['submit'])){

	$url = (isset($_POST['url'])) ? $_POST['url'] : '';

	$rand = rand();

	$playlistName = (isset($_POST['list_name'])) ? $_POST['list_name'] : $rand;
	$rawplaylistName = (isset($_POST['list_name'])) ? $_POST['list_name'] : $rand;


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

	$bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN);


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

			      //echo '<pre>';
			      //print_r($video);
			      //echo '</pre>';
			     
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
			$params->shortDescription = ($video->snippet['description'] != '') ? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', substr(substr($video->snippet['description'],0,strpos($video->snippet['description'],'.')),0,240)) : substr($val,240);
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

			//echo '<pre>';print_r($params);echo '</pre>';

			


			
			try {
			  // Upload the video and save the video ID
			  $res = $bc->createMediaViaApi('video', $params);
			  $id = $res->result;
			  $refVideoId[$key] = $id;
			  //$refVideoId[] = $id;
			} catch(Exception $error) {
			  // Handle our error
			  //echo '<pre>';
			  //print_r($error);
			  //echo '</pre>';
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
	    //echo '<pre>';
	    //echo $error;
	    //echo '</pre>';
	    //die();
	  }

	  $playlistDetailsFinal->playlist_id = $plid;
	  $playlistDetailsFinal->success_YT_video_ids = $refVideoIdArr;
	  $playlistDetailsFinal->error_YT_video_ids = $refErrorvideoKeyYT;

	echo '<pre>';
	print_r($playlistDetailsFinal);
	echo '</pre>';


	

}

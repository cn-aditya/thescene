<?php
error_reporting(0);
$time_start = microtime( true );

$API_READ_TOKEN='ica_Q91p0c-BoCFcCt0M58RLP_YD5vyuSf_qiIhagXKM36A7dzegnQ..';
$API_WRITE_TOKEN= '3w6N6ouRt39sFKgIBOTw98DQjwEgmD6c9RsvSbiSbESiZjiDHHzJXw..';

$url = (isset($_POST['url'])) ? $_POST['url'] : '';

$rand = rand();

$playlistName = (isset($_POST['list_name'])) ? $_POST['list_name'] : $rand;
$rawplaylistName = (isset($_POST['list_name'])) ? $_POST['list_name'] : $rand;


$basePath = '/var/www/html/thescene/ytpl/';
$pathToDownload = 'files/';

$remDir = str_replace(' ','_',$playlistName).'-'.date('m_d_Y_h_i_s');

$subDir = $remDir;
$fullPathToDownload = $basePath . $pathToDownload . $subDir;


exec('mkdir ' .  $pathToDownload.$subDir . '/' );
exec('chmod -R 777 ' . $fullPathToDownload . '/' );



$cmd = '/usr/local/bin/youtube-dl  --output "' . $fullPathToDownload . '/' . '%(id)s--%(title)s.%(ext)s" '. escapeshellcmd($url);
//$cmd = '/usr/local/bin/youtube-dl --output "' . $fullPathToDownload . '/' . '%(id)s.%(ext)s" '. escapeshellcmd($url);
exec($cmd);



$videoId = array();
$files = array();

if (is_dir($fullPathToDownload)) {
    if ($dh = opendir($fullPathToDownload)) {
        while (($file = readdir($dh)) !== false) {
        	
        	$dtls = explode('--',$file);

        	$videoId[] = $dtls[0];
        	$files[] = $dtls[1];
        	
        	//$dtls = $file;

        	//$videoId[] = $dtls;
        	//$files[] = $dtls;
        	
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



$videoDirUrl = 'http://52.76.27.244/ytpl/files/' . $subDir . '/';


require('bc-mapi.php');

$bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN);

//echo '<pre>';print_r($playlistDetails);echo '</pre>';


$refVideoId = array();
$refErrorvideoKeyYT = array();
foreach($playlistDetails->videos as $key=>$val){
	if( (($key!='') && ($val!='')) && (($key!='.') && ($val!='.'))  && (($key!='..') && ($val!='..')) ){

		$name = str_replace(substr($val, -4), '', $val);

		
 
		$params->name = $name;
		$params->referenceId = $key;
		//$params->referenceId = $name;
		$params->shortDescription = $val;
		$params->startDate = null;
		$params->endDate = null;

		$params->renditions = array();

		$url = $videoDirUrl . $key . '--' . ($val);
		//$url = $videoDirUrl . ($val);

		$params->renditions = array(); 
		$params->renditions[0]->displayName = $name;
		$params->renditions[0]->referenceId = $key;
		//$params->renditions[0]->referenceId = $name;
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
    //die();
  }

  $playlistDetailsFinal->playlist_id = $plid;
  $playlistDetailsFinal->success_YT_video_ids = $refVideoIdArr;
  $playlistDetailsFinal->error_YT_video_ids = $refErrorvideoKeyYT;

echo '<pre>';
print_r($playlistDetailsFinal);
echo '</pre>';


echo '<br> Render Time : '.number_format( microtime( true ) - $time_start, 10 );
echo '<br><a href="index.php">Start a new session.</a>';
<?php
//session_destroy();exit;
session_start();
error_reporting(0);
$time_start = microtime( true );

require_once realpath(dirname(__FILE__) . '/src/Google/autoload.php');




/*
 * All credencials
 */
$OAUTH2_CLIENT_ID = '132592719130-nu7v5c2vs9jitab9kao84a3stcbcbc7l.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = 'mxb47Qi__S9QRvUWiIbS1o6t';

$API_READ_TOKEN='ica_Q91p0c-BoCFcCt0M58RLP_YD5vyuSf_qiIhagXKM36A7dzegnQ..';
$API_WRITE_TOKEN= '3w6N6ouRt39sFKgIBOTw98DQjwEgmD6c9RsvSbiSbESiZjiDHHzJXw..';

$url = (isset($_POST['url'])) ? $_POST['url'] : 'https://www.youtube.com/playlist?list=PLyNPmnZUx11V_XYhSSA5kZMk8-ObA2PMO';

$rand = rand();


$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
    FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die('The session state did not match.');
  }

  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: ' . $redirect);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}


if ($client->getAccessToken()) {
  try{













    $basePath = '/var/www/html/ts/local/ytpl/';
    $pathToDownload = 'files/';

    $remDir = str_replace(' ','_',$playlistName).'-'.date('m_d_Y_h_i_s');

    $subDir = $remDir;
    $fullPathToDownload = $basePath . $pathToDownload . $subDir;


    exec('mkdir ' .  $pathToDownload.$subDir . '/' );
    exec('chmod -R 777 ' . $fullPathToDownload . '/' );



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


    require('bc-mapi.php');

    $bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN);

    //echo '<pre>';print_r($playlistDetails);echo '</pre>';


    $refVideoId = array();
    $refErrorvideoKeyYT = array();
    foreach($playlistDetails->videos as $key=>$val){
      if( (($key!='') && ($val!='')) && (($key!='.') && ($val!='.'))  && (($key!='..') && ($val!='..')) ){

        $name = str_replace(substr($val, -4), '', $val);


        
        $videoId = $key;

        // Call the API's videos.list method to retrieve the video resource.
        
        $listResponse = $youtube->videos->listVideos("snippet,statistics,contentDetails,status,topicDetails",
        array('id' => $videoId));

        $resp = $listResponse[0];

        echo '<pre>';
        print_r($resp);

        
     
        $params->name = $name;
        $params->referenceId = $key;
        $params->shortDescription = $resp->snippet['description'];
        $params->startDate = strtotime($resp->snippet['publishedAt']);
        $params->endDate = null;
        $params->tags = (is_array($resp->snippet['tags'])) ?  implode(',', $resp->snippet['tags']) : null;

        $params->renditions = array();

        $url = $videoDirUrl . $key . '--' . ($val);

        $params->renditions = array(); 
        $params->renditions[0]->displayName = $name;
        $params->renditions[0]->referenceId = $key;
        $params->renditions[0]->remoteUrl = $url;
        $params->renditions[0]->videoDuration = '-1' ;
        $params->renditions[0]->videoCodec = 'H264';
        $params->renditions[0]->videoContainer = 'MP4';
        $params->renditions[0]->size = '1024';

        print_r($params);

        //echo '<pre>';print_r($params);echo '</pre>';


        exit;
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


    print_r($refVideoId);


















    } catch (Google_Service_Exception $e) {
      $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
          htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
      $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
          htmlspecialchars($e->getMessage()));
    }

    $_SESSION['token'] = $client->getAccessToken();
    } else {
      // If the user hasn't authorized the app, initiate the OAuth flow
      $state = mt_rand();
      $client->setState($state);
      $_SESSION['state'] = $state;

      $authUrl = $client->createAuthUrl();
      $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
    }
    ?>

    <!doctype html>
    <html>
    <head>
    <title>Video Updated</title>
    </head>
    <body>
      <?=$htmlBody?>
    </body>
    </html>
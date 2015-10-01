<?php

require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');
// Call set_include_path() as needed to point to your client library.
  //require_once '../src/Google/Client.php';
  //require_once '../src/Google/Service/YouTube.php';
  session_start();


/*
 * You can acquire an OAuth 2.0 client ID and client secret from the
 * Google Developers Console <https://console.developers.google.com/>
 * For more information about using OAuth 2.0 to access Google APIs, please see:
 * <https://developers.google.com/youtube/v3/guides/authentication>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
$OAUTH2_CLIENT_ID = '132592719130-nu7v5c2vs9jitab9kao84a3stcbcbc7l.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = 'mxb47Qi__S9QRvUWiIbS1o6t';





// Call set_include_path() as needed to point to your client library.
//require_once 'Google/Client.php';
//require_once 'Google/Service/YouTube.php';
//session_start();


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

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
  try{

    // REPLACE this value with the video ID of the video being updated.
    $videoId = "RM6_Cy-P7zQ";

    // Call the API's videos.list method to retrieve the video resource.
    $listResponse = $youtube->videos->listVideos("snippet,statistics,contentDetails,status,topicDetails",
        array('id' => $videoId));

    //echo '<pre>';print_r($listResponse[0]);echo '</pre>';

    // If $listResponse is empty, the specified video was not found.
    if (empty($listResponse)) {
      $htmlBody .= sprintf('<h3>Can\'t find a video with video id: %s</h3>', $videoId);
    } else {
      // Since the request specified a video ID, the response only
      // contains one video resource.
      $video = $listResponse[0];

      echo '<pre>';
      print_r($video);

      /*$videoSnippet = $video['snippet'];
      $tags = $videoSnippet['tags'];

      // Preserve any tags already associated with the video. If the video does
      // not have any tags, create a new list. Replace the values "tag1" and
      // "tag2" with the new tags you want to associate with the video.
      if (is_null($tags)) {
        $tags = array("tag1", "tag2");
      } else {
        array_push($tags, "tag1", "tag2");
      }

      // Set the tags array for the video snippet
      $videoSnippet['tags'] = $tags;

      // Update the video resource by calling the videos.update() method.
      $updateResponse = $youtube->videos->update("snippet", $video);

      $responseTags = $updateResponse['snippet']['tags'];


    $htmlBody .= "<h3>Video Updated</h3><ul>";
    $htmlBody .= sprintf('<li>Tags "%s" and "%s" added for video %s (%s) </li>',
        array_pop($responseTags), array_pop($responseTags),
        $videoId, $video['snippet']['title']);

    $htmlBody .= '</ul>';
    */
  }
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
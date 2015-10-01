<?php

require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');
require('../bc-mapi.php');
session_start();

$OAUTH2_CLIENT_ID = '132592719130-nu7v5c2vs9jitab9kao84a3stcbcbc7l.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = 'mxb47Qi__S9QRvUWiIbS1o6t';

$API_READ_TOKEN='ica_Q91p0c-BoCFcCt0M58RLP_YD5vyuSf_qiIhagXKM36A7dzegnQ..';
$API_WRITE_TOKEN= '3w6N6ouRt39sFKgIBOTw98DQjwEgmD6c9RsvSbiSbESiZjiDHHzJXw..';





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
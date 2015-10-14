<?php

require_once realpath(dirname(__FILE__) . '/conf.php');



if ($client->getAccessToken()) {
  $_SESSION['token'] = $client->getAccessToken();


  $host  = $_SERVER['HTTP_HOST'];
  $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $extra = 'process.php';
  //header("Location: http://$host$uri/$extra");

  //exit;
  require_once realpath(dirname(__FILE__) . '/process.php');

  $htmlBody = <<<END
  <form action="index.php" method="post">
<p>
  <label>Play List Url</label>
  <input type="text" name="url" value="" required>
</p>
<p>
  <label>Play List Name</label>
  <input type="text" name="list_name" value="" required>
</p>
<p>
  <label>Play List ID</label>
  <input type="text" name="list_id" value="" required>
</p>
<p>
  <label>Assigned Folder Name</label>
  <input type="text" name="folder_name" value="" required>
</p>
<!--<p>
  <label>Assigned Folder ID</label>
  <input type="text" name="folder_id" value="" required>
</p>-->
<p>
  <label>Action</label>
  <input type="submit" name="submit">
</p>
</form>
END;


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


echo $htmlBody;
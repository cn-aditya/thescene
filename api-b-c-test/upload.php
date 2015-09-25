<?php
require('config.php');


// Create an array of meta data from our form fields
  $metaData = array(
    'name' => $_POST['bcVideoName'],
    'shortDescription' => $_POST['bcShortDescription']
  );
  
  // Move the file out of 'tmp', or rename
  rename($_FILES['videoFile']['tmp_name'], '/tmp/' . $_FILES['videoFile']['name']);
  $file = '/tmp/' . $_FILES['videoFile']['name'];
  
  // Create a try/catch
  try {
    // Upload the video and save the video ID
    $id = $bc->createMedia('video', $file, $metaData);
          echo 'New video id: ';
          echo $id;
  } catch(Exception $error) {
    // Handle our error
    echo '<pre>';
    print_r($_FILES);
    echo $error;
    die();
  }



echo '<br> Render Time : '.number_format( microtime( true ) - $time_start, 10 );

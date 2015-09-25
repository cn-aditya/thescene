<?php
require('config.php');

//$videos = $bc->find('allVideos');
$videos = $bc->findAll();

echo '<pre>';
print_r($videos);
//echo 'Total Videos: ' . $bc->total_count . ';';
//echo 'API Calls: ' . $bc->__get('api_calls');

echo 'Render Time : '.number_format( microtime( true ) - $time_start, 10 );

echo '</pre>';
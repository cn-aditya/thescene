<?php
error_reporting(1);

$time_start = microtime( true );

$API_READ_TOKEN='ica_Q91p0c-BoCFcCt0M58RLP_YD5vyuSf_qiIhagXKM36A7dzegnQ..';
$API_WRITE_TOKEN= '3w6N6ouRt39sFKgIBOTw98DQjwEgmD6c9RsvSbiSbESiZjiDHHzJXw..';


require('bc-mapi.php');

$bc = new BCMAPI($API_READ_TOKEN, $API_WRITE_TOKEN);


require('bc-mapi-cache.php');



// Using flat files

//$bc_cache = new BCMAPICache('file', 600, '/var/www/html/test/PHP-MAPI-Wrapper-master/cache/', '.cache');PHP-MAPI-Wrapper-master
//$bc_cache = new BCMAPICache('memcached', 600, 'localhost', NULL, 11211);

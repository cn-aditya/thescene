<?php

$key = urlencode(base64_encode('Slick50!'));

$request = "http://scene.cnidigital.in/ytpl/syncmeta/get_sync_list.php?key={$key}";
//$request = "http://localhost/ts/local/ytpl/syncmeta/get_sync_list.php?key={$key}";


$data = array( 
	'getAccess' => 'syncData',
	'folder'	=> 'vogue', // folder name : default = test
	'dataSet'	=> 'synced', // failed, synced
	'playlist'	=> '', // youtube playlist id
	);


$ch = curl_init($request);
curl_setopt_array($ch, array(
		CURLOPT_CUSTOMREQUEST  => 'POST',
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE,
		CURLOPT_POSTFIELDS => ($data)
	));
$response = curl_exec($ch); 
curl_close($ch);
// Check for errors
if ($response === FALSE) {
	echo "Error: "+$response;
	//die(curl_error($ch));
}

$syncedDataObj =  json_decode($response);

$syncedDataArr = (array)  $syncedDataObj->syncData;

//echo '<pre>';
//print_r(($syncedDataArr));



$data = array( 
	'getAccess' => 'syncData',
	'folder'	=> 'vogue', // folder name : default = test
	'dataSet'	=> 'failed', // failed, synced
	'playlist'	=> '', // youtube playlist id
	);


$ch = curl_init($request);
curl_setopt_array($ch, array(
		CURLOPT_CUSTOMREQUEST  => 'POST',
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE,
		CURLOPT_POSTFIELDS => ($data)
	));
$response = curl_exec($ch); 
curl_close($ch);
// Check for errors
if ($response === FALSE) {
	echo "Error: "+$response;
	//die(curl_error($ch));
}

$failedDataObj =  json_decode($response);

$failedDataArr = (array)  $failedDataObj->syncData;

//echo '<pre>';
//print_r(($failedDataArr));

$match = array_diff_key($syncedDataArr, $failedDataArr);

$diff = array_diff_key($failedDataArr, $syncedDataArr);



echo '<pre>';
echo 'Total failed ID count : ' . count($diff) . '<br>';
print_r(($diff));
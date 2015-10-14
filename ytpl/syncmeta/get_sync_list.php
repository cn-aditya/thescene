<?php
require_once realpath(dirname(__FILE__) . '/conf.php');

$sequrityKey = 'Slick50!';

$link = new mysqli($host_name, $user, $password, $db_name);

$resultSet = array();
$syncData = NULL;
$data = NULL;
$status = NULL;
$reason = NULL;
$dataSet = NULL;
$folder = NULL;
$subQry = '';
$method = NULL;
$total = 0;

if( isset($_GET) && ( !isset($_GET['key']) || ($_GET['key'] == '') ) ){
	$status = 'failure';
	$reason = 'ack_required';
	$resultSet['_empty'] = 'check your credentials';
	$method = '_blank';
} else if( isset($_GET) && ( urldecode(base64_decode($_GET['key'])) != $sequrityKey ) ){
	$status = 'failure';
	$reason = 'ack_failed';
	$resultSet['_empty'] = 'check your credentials';
	$method = '_blank';
} else if( isset($_POST) && ($_POST['getAccess'] == 'syncData') ){

	$folder = ( isset($_POST['folder']) && ($_POST['folder'] != '') ) ? $_POST['folder'] : 'test';

	$subQry .= " AND folder = '{$folder}'";
	$subQry .= ( isset($_POST['playlist']) && ($_POST['playlist'] != '') ) ? " AND yt_pl_id = '{$_POST['playlist']}'" : "";

	switch ($_POST['dataSet']) {
		case 'synced':

			$method = array(
				'dataSet'	=>	$_POST['dataSet'],
				'folder'	=>	$folder,
				'playlist'	=>	( isset($_POST['playlist']) && ($_POST['playlist'] != '') ) ? $_POST['playlist'] : "",
			);

			$dataSet = 'synced';
			$qry = "SELECT yt_id AS youtube, bc_id AS brightcove FROM ts_yt_bc_sync_ref_ids WHERE failed_yt_id = '0'" . $subQry;
			break;

		case 'failed':

			$method = array(
				'dataSet'	=>	$_POST['dataSet'],
				'folder'	=>	$folder,
				'playlist'	=>	( isset($_POST['playlist']) && ($_POST['playlist'] != '') ) ? $_POST['playlist'] : "",
			);

			$dataSet = 'failed';
			$qry = "SELECT yt_id AS youtube, bc_id AS brightcove FROM ts_yt_bc_sync_ref_ids WHERE failed_yt_id = '1'" . $subQry;
			break;
		
		default:

			$method = array(
				'dataSet'	=>	'default',
				'folder'	=>	$folder,
				'playlist'	=>	( isset($_POST['playlist']) && ($_POST['playlist'] != '') ) ? $_POST['playlist'] : "",
			);

			$dataSet = 'synced';
			$qry = "SELECT yt_id AS youtube, bc_id AS brightcove FROM ts_yt_bc_sync_ref_ids WHERE failed_yt_id = '0'" . $subQry;
			break;
	}



	if ($result = $link->query( $qry )) { //MYSQLI_USE_RESULT

	    //printf("Select returned %d rows.\n", $result->num_rows);
	    $total = $result->num_rows;

	    if (!$link->query("SET @a:='this will not work'")) {
	        printf("Error: %s\n", $link->error);
	    }

	    if($result->num_rows > 0){
	    	while($obj = $result->fetch_object()){ 

	    		$resultSet[$obj->youtube] = $obj->brightcove;

		    }
	    }else{
	    	$resultSet['_empty'] = 'no data found';
	    }    

	    $result->close();
	}

	$status = 'success';
	$reason = 'success';

}else{
	$status = 'failure';
	$reason = 'data_param';
	$resultSet['_empty'] = 'Post data missing';
	$method = '_blank';
}



$syncData->syncData = $resultSet;
$syncData->totalCount = $total;
$syncData->apiStatus = $status;
$syncData->reason = $reason;
$syncData->method = $method;
$data = json_encode($syncData);

echo ($data);
exit;
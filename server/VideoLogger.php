<?php
header('Access-Control-Allow-Origin: *');
include ("config.php");

CONST PREFIX = "t";
CONST DATA_POINTS = 45;

$setting = $config;

switch ($_GET['function']) {
case 'registerView':
    registerView($config, $_GET['key']);
    break;
case 'registerViewProfile':
    registerViewProfile($config, $_GET['key'], json_decode($_GET['viewData']), $_GET['duration'], $_GET['trackRate']);
    break;
case 'getVideoEntity':
    getVideoEntity($config, $_GET['key']);
    break;
default:
    break;
    //do nothing.
}

function registerView($config, $key) {
    $con = mysqli_connect('localhost',$config['mysql_user'],$config['mysql_pwd'],$config['mysql_db_name']);
    if (!$con) {
      die('Could not connect: ' . mysqli_error($con));
    }

    $dbChosen = mysqli_select_db($con, $config['mysql_db_name']);

    if(!isset($dbChosen)){
		logResponse($config['can_log'], "<b> Problem with choosing DB</b>");
    }

    $sql = "SELECT * FROM videos WHERE `key` = '".$key."'";
    $result = mysqli_query($con,$sql);

    if(!isset($result)){
		logResponse($config['can_log'], "<b> Error! watch out! </b>");
    } else {
        $row = mysqli_fetch_array($result);
        $sql="UPDATE videos SET count = count + 1 WHERE `key` = '".$key."'";
        $result = mysqli_query($con,$sql);

        if($result){
			logResponse(true, "OK");
        }
    }
    mysqli_close($con);
}

function registerViewProfile($config, $videoId, $viewData, $duration, $trackRate) {
    //need to structure the data here before updating in CB.
    //we keep 30 data points for the time-being.

    $interval = intval($duration/DATA_POINTS);
    $newData = array();
    $pointer = 0;
	$trackRateInSec = $trackRate/1000;

    for ($i = 0; $i<sizeof($viewData); $i+=2){

        $start = $viewData[$i];
        $end = $viewData[$i+1];

        //We are trying to reduce the reduce the probability of getting an edge-case dip or peak
        $intStart = intval($start);
        if($start - $intStart < $trackRateInSec){
            $start = $intStart;
        }

        $pointer = (intval($start/$interval)*$interval);

        while($pointer <= $duration){
            if($pointer >= $start && $pointer < $end){
				if(isset($newData[PREFIX.$pointer])){
					$newData[PREFIX.$pointer] = intval($newData[PREFIX.$pointer]) + 1;
				}else{
					$newData[PREFIX.$pointer] = 1;
				}
                $pointer += $interval;
            }
            if($pointer > $end){
                break;
            }
            if($pointer < $start) {
                //handling corner case when sometimes the pointer sticks to a prev timeline point.
                $pointer += $interval;
            }
        }
    }

    $cb = new Couchbase($config['couchbase_server'].":".$config['couchbase_port']);
    $encodedVideoEntity = $cb->get($videoId."::youtube");
    $currentViewEntity = (array)json_decode($encodedVideoEntity);

    if(sizeof($currentViewEntity) == 0){
        //initialize the entity
        $currentViewEntity = array();
        $currentViewEntity['id'] = $videoId;
        $currentViewEntity['duration'] = $duration;
        $currentViewEntity['dataPoints'] = DATA_POINTS;
        $currentViewEntity['viewMap'] = array();
    }

    $currentViewData = (array)$currentViewEntity['viewMap'];

    foreach ($newData as $key => $value){
        if(!(array_key_exists( $key, $currentViewData))){
            $currentViewData[$key] = $value;
        }else{
            $currentViewData[$key] += $value;
        }
    }

    $currentViewEntity['viewMap'] = $currentViewData;
    $cb->set($videoId."::youtube", json_encode($currentViewEntity));

	logResponse($config['can_log'], "key: ".$videoId."::youtube ");
	logResponse($config['can_log'], "CB updated!");
	logResponse(true, "OK");
}

function getVideoEntity($config, $videoId) {
    $cb = new Couchbase($config['couchbase_server'].":".$config['couchbase_port']);
    $encodedVideoEntity = $cb->get($videoId."::youtube");
    if(isset($encodedVideoEntity)){
        logResponse(true, $encodedVideoEntity);
    }else {
        logResponse(true, "Not found");
    }
}

function logResponse($canLog, $message) {
	if($canLog){
		echo $message;
	}
}
?>

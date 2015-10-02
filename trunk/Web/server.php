<?php

function sendCamImage($data){
	$GLOBALS['type'] = 'src';
	$data = substr($data, 4, strlen($data));
	
	$GLOBALS['src']=$data;
	
}
function sendDrawImage($data){
	$GLOBALS['type'] = 'json';
	$data = substr($data, 3, strlen($data));
	$ListOfPoints = explode("|", $data);
	
	$json = '{ "dessin":[{';
	for ($i=0; $i<count($ListOfPoints)-1; $i++){
		$currentPoint = explode(";", $ListOfPoints[$i]);
		$firstPoint = substr($currentPoint[0], 1, -1);
		$secondPoint = substr($currentPoint[1], 1, -1);
		
		$firstPoint =  explode(",", $firstPoint);
		$secondPoint =  explode(",", $secondPoint);
		
		if ($i != count($ListOfPoints)-2){
		$json .= '"L'.($i+1).'":[{"point1":"'.$firstPoint[0].';'.$firstPoint[1].'"},{"point2":"'.$secondPoint[0].';'.$secondPoint[1].'"}],';
		}
		else{
		$json .= '"L'.($i+1).'":[{"point1":"'.$firstPoint[0].';'.$firstPoint[1].'"},{"point2":"'.$secondPoint[0].';'.$secondPoint[1].'"}]';
		}
	}
	$json.="}]}";
	$GLOBALS['src'] = $json;
	
}
function sendSvgImage($data){
	$GLOBALS['type'] = 'json';
	$data = substr($data, 4, strlen($data));
	
	$GLOBALS['src']=$data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
	$data = file_get_contents('php://input');
	
	$whichFiletoSend = substr($data, 0, 3);
	
	switch ($whichFiletoSend) {
		case "cam":
			sendCamImage($data);
			break;
		case "des":
			sendDrawImage($data);
			break;
		case "svg":
			sendSvgImage($data);
			break;
	}
}
else{
$return = array('error'=>'403',
				'method'=>$_SERVER['REQUEST_METHOD']);
}

echo $GLOBALS['src'];
?>
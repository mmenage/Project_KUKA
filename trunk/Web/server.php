<?php
    
    function sendCamImage($data){
		// Type de donnée
		$GLOBALS['type'] = 'src';
		// On retire l'extension au début du fichier qui permet de différencier le module
		$data = substr($data, 4, strlen($data));
		
		// Donnée en globale afin d'être accessible partout
		$GLOBALS['src']=$data;
		echo $data;
    
    }
    function sendDrawImage($data){
		// Type de donnée
        $GLOBALS['type'] = 'json';
		// On retire l'extension au début du fichier qui permet de différencier le module
        $data = substr($data, 4, strlen($data));
        $ListOfPoints = explode("|", $data);
        
		// Création du jSon
		$json = '{"nbPoints":'.((count($ListOfPoints)-1)*2).",";
        $json .= '"dessin":[{';
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
		// Donnée en globale afin d'être accessible partout
        $GLOBALS['src'] = $json;
        
    }
    function sendSvgImage($data){
		// Type de donnée
        $GLOBALS['type'] = 'json';
		// On retire l'extension au début du fichier qui permet de différencier le module
        $data = substr($data, 4, strlen($data));
        
		// Donnée en globale afin d'être accessible partout
        // $GLOBALS['src']=$data;
		
		function bezierSecondDegree($p0, $p1, $p2){
			$arrayOfPoint = array();
			for($j=0; $j<=1; $j+=0.1){
				$calc = ($p0 * pow((1-$j),2) + 2 * $j*(1-$j) * $p1 + pow($j,2) * $p2);
				array_push($arrayOfPoint, $calc);
			}
			return $arrayOfPoint;
		}
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->load($data);

		$arrayOfX = array();
		$arrayOfY = array();
		
		foreach( $xmlDoc->getElementsByTagName( 'path' ) as $file ) {
			$keywords = preg_split("/[a-zA-Z]/", $file->getAttribute( 'd' ));
			$keywords = array_filter($keywords);
			var_dump($keywords);
			for ($i=1;$i<=count($keywords); $i++){
				if ($i == 1){
					$coordsToAdd = explode(",", $keywords[1]);
					$xToAdd = $coordsToAdd[0];
					$yToAdd = $coordsToAdd[1];
				}
				else{
					$coords = preg_split('/[;, \n]+/', $keywords[$i]);
					$arrayofallx = bezierSecondDegree($xToAdd+$coords[0], $xToAdd+$coords[2], $xToAdd+$coords[4]);
					$arrayofally = bezierSecondDegree($yToAdd+$coords[1], $yToAdd+$coords[3], $yToAdd+$coords[5]);
					$l=0;
					for ($h=0; $h<(count($arrayofally)*2)-2; $h++){
						if ($h==0){
							array_push($arrayOfX , $arrayofallx[$h]);
							array_push($arrayOfY , $arrayofally[$h]);
							echo "h=0 | x:".$arrayofallx[$h]." y:".$arrayofally[$h]."<br/>";
							$l++;
						}
						else{
							// Si c'est impair
							if ($h % 2 != 0){
								array_push($arrayOfX , $arrayofallx[$l]);
								array_push($arrayOfY , $arrayofally[$l]);
								echo "h pas multiple | : x:".$arrayofallx[$l]." y:".$arrayofally[$l]."<br/>";
							}
							// Si c'est pair
							else{
								array_push($arrayOfX , $arrayofallx[$l-1]);
								array_push($arrayOfY , $arrayofally[$l-1]);
								echo "h multiple |x:".$arrayofallx[$l]." y:".$arrayofally[$l]."<br/>";
								$l--;
							}
						$l++;
						}
					}
				}
			}
		}

		var_dump($arrayOfX);
		var_dump($arrayOfY);
		$data = "";
		for ($i=0; $i<count($arrayOfX); $i++){
			// Si c'est impair
			if ($i % 2 != 0){
				$data .='('.$arrayOfX[$i].','.$arrayOfY[$i].')|';
			}
			// Si c'est pair
			else{
				$data .='('.$arrayOfX[$i].','.$arrayOfY[$i].');';
			}
		}

		$ListOfPoints = explode("|", $data);
		
		// Création du jSon
		$json = '{"nbPoints":'.(((count($ListOfPoints)-1)*2)).",";
		$json .= '"dessin":[{';
		for ($i=0; $i<count($ListOfPoints)-1; $i++){
			// if ($i < count($ListOfPoints)-2){
				$currentPoint = explode(";", $ListOfPoints[$i]);
				$firstPoint = substr($currentPoint[0], 1, -1);
				$secondPoint = substr($currentPoint[1], 1, -1);
				
				$firstPoint =  explode(",", $firstPoint);
				$secondPoint =  explode(",", $secondPoint);
				
			if ($i == count($ListOfPoints)-2){
				$json .= '"L'.($i+1).'":[{"point1":"'.$firstPoint[0].';'.$firstPoint[1].'"},{"point2":"'.$secondPoint[0].';'.$secondPoint[1].'"}]';
			}
			else{
				$json .= '"L'.($i+1).'":[{"point1":"'.$firstPoint[0].';'.$firstPoint[1].'"},{"point2":"'.$secondPoint[0].';'.$secondPoint[1].'"}],';
				
			}
		}
		$json.="}]}";
		// Donnée en globale afin d'être accessible partout
		$GLOBALS['src'] = $json;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Récupération du post
        $data = file_get_contents('php://input');
        
        $whichFiletoSend = substr($data, 0, 3);
        
		// Exécution de la fonction en fonction du module
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

    //SERVER EXECUTION

    error_reporting(~E_NOTICE);
    set_time_limit (0);
     
    $address = "172.30.1.121";
    $port = "3031";
    $max_clients = 10;
     
    if(!($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP )))
    {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
         
        die("Couldn't create socket: [$errorcode] $errormsg \n");
    }
     
    echo "Socket created \n";
     
    // Bind the source address
    if( !socket_bind($sock, $address , $port) )
    {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
         
        die("Could not bind socket : [$errorcode] $errormsg \n");
    }
     
    // echo "Socket bind OK \n";
     
    if(!socket_listen ($sock , 1))
    {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
         
        die("Could not listen on socket : [$errorcode] $errormsg \n");
    }
     
    // echo "Socket listen OK \n";
     
    // echo "Waiting for incoming connections... \n";
     
    //array of client sockets
    $client_socks = array();
     
    //array of sockets to read
    $read = array();
     
    //start loop to listen for incoming connections and process existing connections
    while (true) 
    {
        //prepare array of readable client sockets
        $read = array();
         
        //first socket is the master socket
        $read[0] = $sock;
         
        //now add the existing client sockets
        for ($i = 0; $i < $max_clients; $i++)
        {
            if($client_socks[$i] != null)
            {
                $read[$i+1] = $client_socks[$i];
            }
        }
         
        //now call select - blocking call
        if(socket_select($read , $write , $except , null) === false)
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
         
            die("Could not listen on socket : [$errorcode] $errormsg \n");
        }
         
        //if ready contains the master socket, then a new connection has come in
        if (in_array($sock, $read)) 
        {
            for ($i = 0; $i < $max_clients; $i++)
            {
                if ($client_socks[$i] == null) 
                {
                    $client_socks[$i] = socket_accept($sock);
                     
                    //display information about the client who is connected
                    if(socket_getpeername($client_socks[$i], $address, $port))
                    {
                        // echo "Client $address : $port is now connected to us.\n";
							//###### socket_write($client_socks[$i] , $GLOBALS['src']);
							// break;
						 // $json = array();
                        // $point1 = array(
                        // 'x' => '341',
                        // 'y' => '387',
                        // );
                        // $point2 = array(
                        // 'x' => '97',
                        // 'y' => '31',
                        // );
                        // $point3 = array(
                        // 'x' => '174',
                        // 'y' => '456',
                        // );
                        // array_push($json, $point1);
                        // array_push($json, $point2);
                        // array_push($json, $point3);
                        // $jsonstring = '{"dessin":'.json_encode($json).'}'."\n";
                        // echo "Client $address : $port is now connected to us.\n";
                        // socket_write($client_socks[$i] , $jsonstring);
                        socket_write($client_socks[$i] , $GLOBALS['src']."\n");
						echo $GLOBALS['src'];
                        //Those 2 lines below allow to don't change port each draw sent
                        $linger = array ('l_linger' => 0, 'l_onoff' => 1);
                        socket_set_option($client_socks[$i], SOL_SOCKET, SO_LINGER, $linger);
						socket_close($client_socks[$i]);
                        echo "Socket closed";
						exit(0);
                        break;
                    }
                }
            }
        }
    }
?>
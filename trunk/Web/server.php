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
		// Donnée en globale afin d'être accessible partout
        $GLOBALS['src'] = $json;
		echo $json;
        
    }
    function sendSvgImage($data){
		// Type de donnée
        $GLOBALS['type'] = 'json';
		// On retire l'extension au début du fichier qui permet de différencier le module
        $data = substr($data, 4, strlen($data));
        
		// Donnée en globale afin d'être accessible partout
        $GLOBALS['src']=$data;
		echo $data;
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

    error_reporting(~E_NOTICE); //Error management
    set_time_limit (0); //No limit script execution
     
    $address = "192.168.1.10"; //set adress server
    $port = "4035"; //set port
    $max_clients = 1; //set max client

    //###NETWORK-SOCKET ERRORS MANAGEMENT
     
        if(!($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP )))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
             
            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }
         
        echo "Socket created [OK]\n";
         
        // Binding source address
        if( !socket_bind($sock, $address , $port) )
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
             
            die("Couldn't bind socket : [$errorcode] $errormsg \n");
        }
         
        echo "Socket bind [OK]\n";
         
        if(!socket_listen ($sock , 1))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
             
            die("Couldn't listen on socket : [$errorcode] $errormsg \n");
        }
    //###END ERRORS MANAGEMENT
     
    echo "Socket listen [OK]\n";
     
    echo "Waiting for incoming connections... \n";
     
    //create clients sockets array
    $client_socks = array();
     
    //creat read socket array
    $read = array();
     
    //Listening loop awaiting connection
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
                     
                    //displa@y information about the client who is connected
                    if(socket_getpeername($client_socks[$i], $address, $port))
                    {
                        //echo $GLOBALS['src'];
                        echo "Client $address : $port is now connected to us.\n";
                        $socket_test_flush=socket_write($client_socks[$i] , $GLOBALS['src']);
                        exit(0);
                        break;
                    }
                     
                    // $message_server = socket_read($client_socks[$i] , 1024);
                    // socket_write($client_socks[$i], $message_server);
                    socket_shutdown($sock, 2);
                    socket_close($sock);
                    socket_close($client_socks[i]);
                    socket_close($socket_test_flush);
                }
            }
        }
    }
?>
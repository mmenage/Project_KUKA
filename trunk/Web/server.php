<?php
    
    function sendCamImage($data){
		$GLOBALS['type'] = 'src';
		$data = substr($data, 4, strlen($data));
    
		$GLOBALS['src']=$data;
		echo $json;
    
    }
    function sendDrawImage($data){
        $GLOBALS['type'] = 'json';
        $data = substr($data, 4, strlen($data));
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
		echo $json;
        
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

    //SERVER EXECUTION

    error_reporting(~E_NOTICE);
    set_time_limit (0);
     
    $address = "192.168.1.10";
    $port = "4035";
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
     
    echo "Socket bind OK \n";
     
    if(!socket_listen ($sock , 1))
    {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
         
        die("Could not listen on socket : [$errorcode] $errormsg \n");
    }
     
    echo "Socket listen OK \n";
     
    echo "Waiting for incoming connections... \n";
     
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
                        echo $GLOBALS['src']."bite";
                        echo "Client $address : $port is now connected to us.\n";
                        $socket_test_flush=socket_write($client_socks[$i] , $GLOBALS['src']);
                        break;
                    }
                     
                    //Send Welcome message to client
                    // $message .= "{'dessin':[{p1:'0,0',p2:'1,0',p3:'1,1'}]}";
                    // socket_write($client_socks[$i] , $message);
                    //break;

                    $message_server = socket_read($client_socks[$i] , 1024);
                    socket_write($client_socks[$i], $message_server);
                    socket_shutdown($sock, 2);
                    socket_close($sock);
                    socket_close($client_socks[i]);
                }
            }
        }
     
        // //check each client if they send any data
        // for ($i = 0; $i < $max_clients; $i++)
        // {
        //     if (in_array($client_socks[$i] , $read))
        //     {
        //         $input = socket_read($client_socks[$i] , 1024);
                 
        //         if ($input == null) 
        //         {
        //             //zero length string meaning disconnected, remove and close the socket
        //             unset($client_socks[$i]);
        //             socket_close($client_socks[$i]);
        //         }
     
        //         $n = trim($input);
     
        //         $output = "Sent by client : $input";
                 
        //         echo "Sending output to client : $input \n";
                 
        //         //send response to client
        //         socket_write($client_socks[$i] , $output);
        //     }
        // }
    }
?>
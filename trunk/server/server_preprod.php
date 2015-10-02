<?php   
    error_reporting(~E_NOTICE);
    set_time_limit (0);
     
    $address = "172.30.1.121";
    $port = "4020";
    $max_clients = 1;
     
    if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
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
                        $json = array();
                        $point1 = array(
                        'x' => '200',
                        'y' => '300',
                        );
                        $point2 = array(
                        'x' => '140',
                        'y' => '230',
                        );
                        $point3 = array(
                        'x' => '180',
                        'y' => '456',
                        );
                        array_push($json, $point1);
                        array_push($json, $point2);
                        array_push($json, $point3);
                        $jsonstring = '{"dessin":'.json_encode($json).'}'."\n";
                        echo $jsonstring;
                        //die();
                        $array_coord = array($point1, $point2, $point3);
                        echo $array_coord[1];

                        echo "Client $address : $port is now connected to us.\n";
                        $message = "{\"dessin\":[{\"x\":0,\"y\":0},{\"x\":0,\"y\":1},{\"x\":1,\"y\":0}]}\n";
                        $socket_test_flush=socket_write($client_socks[$i] , $jsonstring);
                        break;
                    }
                     
                    //Send Welcome message to client
                    // $message .= "{'dessin':[{p1:'0,0',p2:'1,0',p3:'1,1'}]}";
                    // socket_write($client_socks[$i] , $message);
                    //break;

                    $message_server = socket_read($client_socks[$i] , 1024);
                    socket_write($client_socks[$i], $message_server);
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
<?php

chdir(dirname(__FILE__) . '/../');

include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");

set_time_limit(0);

// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME); 

include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
 
$ctl = new control_modules();

echo date("H:i:s") . " running " . basename(__FILE__) . "\n";

include_once(DIR_MODULES.'orvibo/orvibo.class.php');
$orvibo = new orvibo();

$orvibo->getConfig();
if (!$orvibo->config['API_ENABLE']) {
 $db->Disconnect();
 exit;
}


$orvibo->ip=$orvibo->config['API_URL'];
$orvibo->port=10000;

//Create a UDP socket
if(!($sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
 
echo "Socket created \n";
 
// Bind the source address
if( !socket_bind($sock, $orvibo->ip , $orvibo->port) )
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not bind socket : [$errorcode] $errormsg \n");
}

echo "Socket bind (".$orvibo->ip.":".$orvibo->port.") OK \n";

socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);

$discover=1;
 


$subscribed=array();

//Do some communication, this loop can handle multiple clients
while(1)
{

    setGlobal((str_replace('.php', '', basename(__FILE__))).'Run', time(), 1);
    if ($discover) {
     $orvibo->discover($sock);
     $discover=0;
    }

    echo date('H:i:s')." Waiting for data ... \n";
     
    //Receive some data
    $r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
    if ($remote_ip!=$orvibo->ip) {
     $orvibo->processMessage($buf, $remote_ip, $sock);
    }

   if (file_exists('./reboot') || $_GET['onetime']) 
   {
      $db->Disconnect();
      exit;
   }


}
 
socket_close($sock);

DebMes("Unexpected close of cycle: " . basename(__FILE__));

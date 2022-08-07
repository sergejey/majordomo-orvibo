<?php

// Script to change WiFi setting of Orvibo S20 socket and Orvibo AllOne IR blaster
// How to use:
// 1. Change device into pairing mode (holding the button for a few seconds till it will make new wifi access point)
// 2. Connect to device's access point
// 3. Run the script below (adjust home network details)
// many thanks to: https://git.stikonas.eu/andrius/s20

$new_wifi='my_home_network';   // wifi SSID (set your own)
$new_wifi_password = 'my_home_password';  // wifi password (set your own)

$local_ip = '10.10.100.150';   // no need to change?
$remote_ip = '10.10.100.254';  // no need to change?

$new_wifi_security = 'WPA2PSK,AES';  // wifi security (no need to change?)


$port = 48899;

if (!($sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}

if (!socket_bind($sock, $local_ip, $port)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Could not bind socket : [$errorcode] $errormsg \n");
}

socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 10, "usec" => 0));


$reply = sendMessage("HF-A11ASSISTHREAD");
sleep(1);
$reply = sendMessage("+ok");
sleep(1);
$reply = sendMessage("AT+WSSSID=".$new_wifi."\r");
sleep(1);
$reply = sendMessage("AT+WSKEY=".$new_wifi_security.",".$new_wifi_password."\r");
sleep(1);
$reply = sendMessage("AT+WMODE=STA\r");
sleep(1);
$reply = sendMessage("AT+Z\r");


socket_close($sock);

function sendMessage($message) {
    global $sock;
    global $remote_ip;
    global $port;

    echo "Sending: $message\n";
    $payload = $message;
    socket_sendto($sock, $payload, strlen($payload), 0, $remote_ip, $port);
    @$r = socket_recvfrom($sock, $buf, 512, 0, $ip, $port);
    echo "Reply: ".$buf."\n";
    return $buf;
}

function makePayload($data)
{
    $res = '';
    foreach ($data as $v) {
        $res .= chr($v);
    }
    return $res;
}

function HexStringToArray($buf)
{
    $res = array();
    for ($i = 0; $i < strlen($buf) - 1; $i += 2) {
        $res[] = (hexdec($buf[$i] . $buf[$i + 1]));
    }
    return $res;
}

function HexStringToString($buf)
{
    $res = '';
    for ($i = 0; $i < strlen($buf) - 1; $i += 2) {
        $res .= chr(hexdec($buf[$i] . $buf[$i + 1]));
    }
    return $res;
}


function binaryToString($buf)
{
    $res = '';
    for ($i = 0; $i < strlen($buf); $i++) {
        $num = dechex(ord($buf[$i]));
        if (strlen($num) == 1) {
            $num = '0' . $num;
        }
        $res .= $num;
    }
    return $res;
}
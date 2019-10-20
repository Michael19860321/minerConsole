<?php
/**
 * 局域网广播，发送mac地址
 * Added by Michael
 * Oct. 19th 2019
 */

$macAddr = '';
$port = '10000';

// for miner linux os only
@exec("ifconfig -a", $result);

$tem = array();
foreach($result as $val){
    if(preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i",$val,$tem) ){
        $macAddr = $tem[0];
        break;
    }
}

$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
socket_sendto($sock, $macAddr, strlen($macAddr), 0, '255.255.255.255', $port);

?>
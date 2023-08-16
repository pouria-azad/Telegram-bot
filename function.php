<?php
define('API_TOKEN', '6546866682:AAGJD4uISP2U4RsOU8JC0fWNZ8VirfjGZnE');
define('API_REQUEST', 'https://api.telegram.org/bot'.API_TOKEN.'/');

function sendMessage($Chat_id , $Message){
    $Method = 'sendMessage';
    $Request_to_server = API_REQUEST.$Method."?"."chat_id=".$Chat_id."&"."text=".$Message;
    file_get_contents($Request_to_server);
}
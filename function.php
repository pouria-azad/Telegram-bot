<?php
define('API_TOKEN', '6546866682:AAGJD4uISP2U4RsOU8JC0fWNZ8VirfjGZnE');
define('API_REQUEST', 'https://api.telegram.org/bot'.API_TOKEN.'/');

function sendMessage($Message_id , $text){
    $Method = 'sendMessage';
    $Request_to_server = API_REQUEST.$Method."?"."chat_id=".$Message_id."&"."text=".$text;
    file_get_contents($Request_to_server);
}

function startWellcome($Message_id , $text , $keyboard){
    $Method = 'sendMessage';
    $reply_markup = json_encode($keyboard);
    $Request_to_server = API_REQUEST.$Method."?"."chat_id=".$Message_id."&"."text=".$text."&"."reply_markup=".$reply_markup;
    file_get_contents($Request_to_server);
}

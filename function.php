<?php
define('API_TOKEN', '6546866682:AAGJD4uISP2U4RsOU8JC0fWNZ8VirfjGZnE');
define('API_REQUEST', 'https://api.telegram.org/bot'.API_TOKEN.'/');

function sendMessage($Message_id , $text){
    $Method = 'sendMessage';
    $Request_to_server = API_REQUEST.$Method."?"."chat_id=".$Message_id."&"."text=".$text;
    file_get_contents($Request_to_server);
}

function startWellcome($Message_id , $text , $keyboard , $Message_message_id){
    $Method = 'sendMessage';
    $arr_keyboard = array("keyboard"=>$keyboard , "resize_keyboard"=>true , "one_time_keyboard"=>true);
    $reply_markup = json_encode($arr_keyboard);
    $Request_to_server = API_REQUEST.$Method."?"."chat_id=".$Message_id."&"."text=".$text."&"."reply_markup=".$reply_markup."&"."reply_to_message_id=".$Message_message_id;
    file_get_contents($Request_to_server);
}

function getChatMember($chat_id , $user_id){
    $Method = 'getChatMember';
    $Request_to_server = API_REQUEST.$Method."?"."chat_id=".$chat_id."&"."user_id=".$user_id;
    return file_get_contents($Request_to_server);
}

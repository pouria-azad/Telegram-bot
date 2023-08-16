<?php
include './function.php';
$Content = file_get_contents('php://input');
$Object = json_decode($Content , true);
$Message_id = $Object['message']['from']['id'];
$Message_message_id = $Object['message']['message_id'];


$Message_entities = $Object['message']['entities'] ?? false;

if($Message_entities != false){
    if($Object['message']['text'] == '/start'){
        $Keyboard = [ ['عضویت در گروه یادآور'] , ['درباره'] ];
        startWellcome($Message_id , "با سلام به ربات یادآور خوش آمدید.\nلطفا یکی از گزینه های زیر را انتخاب نمایید:" , $Keyboard , $Message_message_id);

}}

//sendMessage($Message_id, $Content);
<?php
include './function.php';
$Content = file_get_contents('php://input');
$Object = json_decode($Content , true);
$Message_id = $Object['message']['from']['id'];

$Message_entities = $Object['message']['entities'] ?? false;
if($Message_entities != false){
    if($Object['message']['text'] == '/start'){
        $keyboard = [ ['عضویت در گروه یادآور'] , ['درباره'] ];
        startWellcome($Message_id , "test" , $keyboard);
}}

//sendMessage($Message_id, $Content);
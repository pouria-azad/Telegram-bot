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
$switch = false;
if($Object['message']['text'] == 'عضویت در گروه یادآور' && $switch == false){
    $switch = true;
    sendMessage($Message_id , 'لطفا نام و نام خانوادگی خود را ارسال نمایید:');
}
if ($switch == true){
    $text = 'نام و نام خانودادگی شما: '.$Object['message']['text'].' ثبت شد.';
    sendMessage($Message_id, $text);
    $switch = false

}

//sendMessage($Message_id, $Content);
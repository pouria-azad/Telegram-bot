<?php
include "./function.php";
include "./config.php";

$Content = file_get_contents('php://input');
$Object = json_decode($Content, true);
$Message_id = $Object['message']['from']['id'];
$Message_message_id = $Object['message']['message_id'];
$Message_entities = $Object['message']['entities'] ?? false;
// $Date = $Object['date'];

sendMessage("1178581717", "1");


if ($Message_entities) {
    if ($Object['message']['text'] == '/start') {

        sendMessage("1178581717", "2");

        try {
            $sql = "INSERT INTO `status`(`chat_id`, `status`) VALUES (? , ?)";
            $pdo = $conn->prepare($sql);
            $pdo->bindValue(1, $Message_id);
            $pdo->bindValue(2, "0");
            $pdo->execute();
            sendMessage("1178581717", "New record created successfully");
            // echo "New record created successfully";
        } catch (PDOException $e) {
            //   echo $sql . "<br>" . $e->getMessage();
            sendMessage("1178581717", $sql . "<br>" . $e->getMessage());
        }

        $conn = null;
        $Keyboard = [['مدیریت لیست اعضا'], ['درباره']];
        startWellcome($Message_id, "با سلام به ربات یادآور خوش آمدید.\nلطفا یکی از گزینه های زیر را انتخاب نمایید:", $Keyboard, $Message_message_id);
    }
}
// $switch = false;
// if($Object['message']['text'] == 'عضویت در گروه یادآور' && $switch == false){
//     $switch = true;
//     sendMessage($Message_id , 'لطفا نام و نام خانوادگی خود را ارسال نمایید:');
// }
// if ($switch == true){
//     $text = 'نام و نام خانودادگی شما: '.$Object['message']['text'].' ثبت شد.';
//     sendMessage($Message_id, $text);
//     $switch = false

// }

//sendMessage($Message_id, $Content);

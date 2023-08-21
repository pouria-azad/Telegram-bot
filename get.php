<?php
include "./function.php";
include "./config.php";

$Content = file_get_contents('php://input');
$Object = json_decode($Content, true);
//message
$Message_id = $Object['message']['from']['id'] ?? false;
$Message_message_id = $Object['message']['message_id'] ?? false;
$Message_entities = $Object['message']['entities'] ?? false;
$Date = $Object['message']['date'] ?? false;
//new_chat_member
if (isset($Object['message']['new_chat_members']) && $Object['message']['new_chat_members']['is_bot'] == false) {
    $newMembers = $Object['message']['new_chat_members'];
    sendMessage("1178581717", "halghe");
    foreach ($newMembers as $newMember) {
        $userId = $newMember['id'];
        $username = isset($newMember['username']) ? $newMember['username'] : '';
        $firstName = $newMember['first_name'];
        $lastName = isset($newMember['last_name']) ? $newMember['last_name'] : '';
        sendMessage("1178581717", "foreach");
        $rrr = "";
        try {
            $pdo = $conn->prepare("INSERT INTO `users`(`chat_id`, `username`, `fullname`) VALUES (? , ? , ?)");
            $pdo->bindValue(1, $userId);
            $pdo->bindValue(2, $username);
            $pdo->bindValue(3, $firstName . ' ' . $lastName);
            $pdo->execute();
            $rrr = "New record created successfully";
            sendMessage("1178581717", "try");
        } catch (PDOException $e) {
            $rrr = $e->getMessage();
        }
        sendMessage("1178581717", "end");
        $pdo = $conn->prepare("INSERT INTO `kj`(`text`) VALUES (?)");
        $pdo->bindValue(1, $rrr);
        $pdo->execute();
    }
}
//callback
$Callback_chat_id = $Object['callback_query']['from']['id'] ?? false;
$Callback_data = $Object['callback_query']['data'] ?? false;
$Callback_id = $Object['callback_query']['id'] ?? false;
$Callback_date = $Object['callback_query']['message']['date'] ?? false;


$pdo = $conn->prepare("INSERT INTO `kj`( `log`) VALUES ( ? )");
$pdo->bindValue(1, $Content);
$pdo->execute();

sendMessage("1178581717", "1");

try {
    $sql = "SELECT `chat_id`,`status` FROM `status` WHERE `chat_id`= ? LIMIT 1";
    $pdo = $conn->prepare($sql);
    $pdo->bindValue(1, $Message_id);
    $pdo->execute();
    $result = $pdo->setFetchMode(PDO::FETCH_ASSOC);
    $array = $pdo->fetchAll();
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}



if (($Message_entities && $Object['message']['text'] == '/start') || ($array[0]['status'] == "1" && $Object['message']['text'] == "بازگشت")) {
    changeStatus($array, $conn, $Message_id,  $Date, "0");
    //////
    $Keyboard = [['مدیریت لیست اعضا'], ['درباره']];
    startWellcome($Message_id, "با سلام به ربات یادآور خوش آمدید.  لطفا یکی از گزینه های زیر را انتخاب نمایید:", $Keyboard, $Message_message_id);
} elseif ($array[0]['status'] == "0" && $Object['message']['text'] == 'مدیریت لیست اعضا') {

    changeStatus($array, $conn, $Message_id,  $Date, "1");
    //////
    $Inline_keyboard = [
        [['text' => 'بروزرسانی لیست اعضا', 'callback_data' => "update"], ['text' => 'دریافت لیست اعضا', 'callback_data' => "recive"]]
    ];
    $Keyboard = [["بازگشت"]];
    startWellcome($Message_id, "/", $Keyboard, $Message_message_id);
    startWellcomeinline($Message_id, "test999", $Inline_keyboard, $Message_message_id);
}
// data 
elseif ($Callback_chat_id && $Callback_data) {
    $array = [];

    try {
        $sql = "SELECT * FROM `users`";
        $pdo = $conn->prepare($sql);
        $pdo->execute();
        $result = $pdo->setFetchMode(PDO::FETCH_ASSOC);
        $array = $pdo->fetchAll();
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }


    switch ($Callback_data) {
        case "update":
            $stmt = $conn->prepare("UPDATE `status` SET `date`= ? ,`status`= ? WHERE `chat_id`= ?");
            $stmt->bindValue(1, $Callback_date);
            $stmt->bindValue(2, "01");
            $stmt->bindValue(3, $Callback_chat_id);
            $stmt->execute();

            //start update
            foreach ($array as $users) {
                $status = getChatMember(-1001454096414, $users['chat_id']);
                if ($status['ok'] && $status['result']['user']['username']) {
                    try {
                        $sql = "UPDATE `users` SET `username`= ? WHERE `chat_id`= ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindValue(1, $status['result']['user']['username']);
                        $stmt->bindValue(2, $users['chat_id']);
                        $stmt->execute();
                    } catch (PDOException $e) {
                        sendMessage("1178581717", $sql . "<br>" . $e->getMessage());
                    }
                }
            }
            answerCallbackQuery($Callback_id, "لیست اعضا با موفقیت آپدیت شد!");
            //end update
            break;
        case "recive":
            $y97 = [];
            $y98 = [];
            $y99 = [];
            $y00 = [];
            foreach ($array as $users) {
                if ($users['entry_year'] == "1397") {
                    $y97[] = [$users['fullname_fa'], $users['username']];
                } elseif ($users['entry_year'] == "1398") {
                    $y98[] = [$users['fullname_fa'], $users['username']];
                } elseif ($users['entry_year'] == "1399") {
                    $y99[] = [$users['fullname_fa'], $users['username']];
                } elseif ($users['entry_year'] == "1400") {
                    $y00[] = [$users['fullname_fa'], $users['username']];
                }
            }

            answerCallbackQuery($Callback_id, "لیست اعضا با موفقیت ارسال شد!");
            year("1397", $y97);
            year("1398", $y98);
            year("1399", $y99);
            year("1400", $y00);

            break;
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

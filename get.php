<?php
include "./function.php";
include "./config.php";
include "./jdf.php";

$Content = file_get_contents('php://input');
$Object = json_decode($Content, true);
//message
$Message_id = $Object['message']['from']['id'] ?? null;
$Message_fname = $Object['message']['from']['first_name'] ?? null;
$Message_message_id = $Object['message']['message_id'] ?? null;
$Message_entities = $Object['message']['entities'] ?? null;
$Date = $Object['message']['date'] ?? null;
$Date = jdate('Y-m-d H:i:s', $Date, "", "", "en");
//new_chat_member
if (isset($Object['message']['new_chat_members']) && $Object['message']['new_chat_members']['is_bot'] == false) {
    $newMembers = $Object['message']['new_chat_members'];
    foreach ($newMembers as $newMember) {
        $userId = $newMember['id'];
        $username = isset($newMember['username']) ? $newMember['username'] : '';
        $firstName = $newMember['first_name'];
        $lastName = isset($newMember['last_name']) ? $newMember['last_name'] : '';
        $rrr = "";
        try {
            $pdo = $conn->prepare("INSERT INTO `users`(`chat_id`, `username`, `fullname`) VALUES (? , ? , ?)");
            $pdo->bindValue(1, $userId);
            $pdo->bindValue(2, $username);
            $pdo->bindValue(3, $firstName . ' ' . $lastName);
            $pdo->execute();
            $rrr = "New record created successfully";
        } catch (PDOException $e) {
            $rrr = $e->getMessage();
        }
        logi($conn, "join user", $rrr, $Content, $Date);
    }
}
//callback
$Callback_chat_id = $Object['callback_query']['from']['id'] ?? null;
$Callback_data = $Object['callback_query']['data'] ?? null;
$Callback_id = $Object['callback_query']['id'] ?? null;
$Callback_date = $Object['callback_query']['message']['date'] ?? null;
if (isset($Callback_date))
    $Callback_date = jdate('Y-m-d H:i:s', $Datestamp, "", "", "en");

sendMessage("1178581717", "1");
//user-status
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
//is_admin
try {
    $pdo = $conn->prepare("SELECT `status` FROM `users` WHERE `chat_id`= ? LIMIT 1");
    if (isset($Message_id))
        $pdo->bindValue(1, $Message_id);
    else
        $pdo->bindValue(1, $Callback_chat_id);
    $pdo->execute();
    $is_admin = $pdo->fetchAll();
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}
// logi($conn, "is admin test", $is_admin, "", $Date);
//کلید استارت یا بازگشت
if (($Message_entities && $Object['message']['text'] == '/start') || ($array[0]['status'] == "1" && $Object['message']['text'] == "بازگشت")) {
    $array = getStatus($conn, $Message_id);
    changeStatus($array, $conn,  $Date, "0", $Message_id);
    //////
    $Keyboard = [['مدیریت لیست اعضا'], ['درباره']];
    startWellcome($Message_id, "با سلام به ربات یادآور خوش آمدید.  لطفا یکی از گزینه های زیر را انتخاب نمایید:", $Keyboard, $Message_message_id);
} //&& $is_admin[0]['status']
elseif ($array[0]['status'] == "0" && $Object['message']['text'] == 'مدیریت لیست اعضا') {
    $array = getStatus($conn, $Message_id);
    changeStatus($array, $conn,  $Date, "1", $Message_id);
    //////
    $Inline_keyboard = [
        [
            ['text' => 'بروزرسانی لیست اعضا', 'callback_data' => "update-0"],
            ['text' => 'دریافت لیست اعضا', 'callback_data' => "recive-0"],
        ],
        [
            ['text' => 'بروزرسانی لیست مدیران', 'callback_data' => "updatead-0"],
            ['text' => 'دریافت لیست مدیران', 'callback_data' => "recivead-0"],
        ]
    ];
    $Keyboard = [["بازگشت"]];
    startWellcome($Message_id, "/", $Keyboard, $Message_message_id);
    $text = $Message_fname . " عزیز در این بخش شما میتوانید اعضای گروه رو مدیریت نمایید";
    startWellcomeinline($Message_id, $text, $Inline_keyboard, $Message_message_id);
    //&& !$is_admin[0]['status']
} elseif ($array[0]['status'] == "0" && $Object['message']['text'] == 'مدیریت لیست اعضا') {
    sendMessage($Message_id, "شما به این بخش دسترسی ندارید!");
}
// data && $is_admin[0]['status']
elseif ($Callback_chat_id && $Callback_data) {
    $array = [];

    try {
        $sql = "SELECT * FROM `users` ORDER BY `users`.`fullname_fa` ASC";
        $pdo = $conn->prepare($sql);
        $pdo->execute();
        //$result = $pdo->setFetchMode(PDO::FETCH_ASSOC);
        $array = $pdo->fetchAll();
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
    $Callback_data = explode('-', $Callback_data);
    if ($Callback_data[1] == "0") {
        switch ($Callback_data[0]) {
            case "update":
                //start update
                foreach ($array as $users) {
                    $status = getChatMember(-1001454096414, $users['chat_id']);
                    if ($status['ok'] && $status['result']['user']['username']) {
                        try {
                            $stmt = $conn->prepare("UPDATE `users` SET `username`= ? WHERE `chat_id`= ?");
                            $stmt->bindValue(1, $status['result']['user']['username']);
                            $stmt->bindValue(2, $users['chat_id']);
                            $stmt->execute();
                        } catch (PDOException $e) {
                            sendMessage("1178581717",  "<br>" . $e->getMessage());
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
                year("1397", $y97, $Callback_chat_id);
                year("1398", $y98, $Callback_chat_id);
                year("1399", $y99, $Callback_chat_id);
                year("1400", $y00, $Callback_chat_id);

                break;
            case "recivead":
                $array = getChatAdministrators("-1001454096414");

                $base0 = "لیست ادمین ها: " . "%0A";
                $byek = "%0A2. MDVR%20 @mdvr_9980%0Aعنوان ادمین در گروه: ادمین۹۹%0A%0A3. ._.%20 @yeganeh_sft%0Aعنوان ادمین در گروه: ادمین ۱۴۰۱%0A%0A4. 🍁Y.N%20 @YGN_NF%0Aعنوان ادمین در گروه: %0A%0A5. Ｔｒｉｎｉｔｙ%20 @Tri_nit_y%0Aعنوان ادمین در گروه: ادمین۱۴۰۰%0A%0A6. ⚜ Maleki_Z ⚜%20 @Z_Maleki_1021%0Aعنوان ادمین در گروه: ادمین ۹۹%0A%0A7. 「𝑷𝒐𝒖𝒓𝒊𝒂 𝑨𝒛𝒂𝒅」 @Pouria_Azad%0Aعنوان ادمین در گروه: ادمین ۹۹%0A%0A8. Somayeh Jamali vahid @Somayeh_6603%0Aعنوان ادمین در گروه: ادمین ۱۴۰۰%0A%0A9. Mohadeseh%20 @Mohiiii_na%0Aعنوان ادمین در گروه: ادمین ۹۹%0A%0A10. Rahmat%20 @wolf2022%0Aعنوان ادمین در گروه: ادمین ۹۹%0A%0A11. Arman Hosseyni @arman_hosseyni%0Aعنوان ادمین در گروه: ادمین ۹۹%0A";
                // foreach ($array['result'] as $key => $admins) {
                //     $lname = $admins["user"]["last_name"] ?? "";
                //     $username = $admins["user"]["username"] ?? "";
                //     $cutsom_title = $admins["custom_title"] ?? "";
                //     if (!$admins["user"]["is_bot"]) {
                //         $byek =  $byek . "%0A" . ($key + 1) . ". " . $admins["user"]["first_name"] . " " . $lname . " @" . $username . "%0A"
                //             . "عنوان ادمین در گروه: " . $cutsom_title . "%0A";
                //     }
                // }


                logi($conn, "conn", sendMessage($Callback_chat_id, $byek), sendMessage($Callback_chat_id, $byek), $Date);
                answerCallbackQuery($Callback_id, "لیست ادمین ها با موفقیت ارسال شد!");
                
                
                break;
            case "updatead":
                $array = getChatAdministrators("-1001454096414");
                foreach ($array['result'] as $key => $admins) {
                    try {
                        $stmt = $conn->prepare("UPDATE `users` SET `status`= '1' WHERE `chat_id`= ?");
                        $stmt->bindValue(1, $admins["user"]["id"]);
                        $stmt->execute();
                    } catch (PDOException $e) {
                        logi($conn, "admin update error", $e->getMessage(), "", $Callback_date);
                    }
                }

                // logi($conn, "conn", sendadmins($Callback_chat_id, $base0), sendadmins($Callback_chat_id, $base0), $Date);
                // $kir = strval($base0 . $byek);
                // logi($conn, "conn", sendadmins($Callback_chat_id, $kir), sendadmins($Callback_chat_id, $kir), $Date);
                answerCallbackQuery($Callback_id, "لیست ادمین ها با موفقیت بروزرسانی شدند!");
                
                break;
        }
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

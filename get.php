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
$Callback_message_message_id = $Object['callback_query']['message']['message_id'] ?? null;
// $Callback_chat_idd = $Object['callback_query']['chat']['id'] ?? null;
$Callback_date = $Object['callback_query']['message']['date'] ?? null;
if (isset($Callback_date))
    $Callback_date = jdate('Y-m-d H:i:s', $Datestamp, "", "", "en");

// sendMessage("1178581717", "1");
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
if (($Message_entities && $Object['message']['text'] == '/start') || (in_array($array[0]['status'], ["-1", "1", "2"]) && ($Object['message']['text'] == "بازگشت به منوی اصلی"))) {
    changeStatus($array, $conn,  $Date, "0", $Message_id);
    //////
    $Keyboard = [
        ['عضویت در گروه یادآور'], ['مدیریت لیست اعضا'], ['درباره']
    ];
    startWellcome($Message_id, "با سلام به ربات یادآور خوش آمدید.  لطفا یکی از گزینه های زیر را انتخاب نمایید:", $Keyboard, $Message_message_id);
    deleteMessage($Message_id, ($Message_message_id - 1));
} //
elseif ($array[0]['status'] == "0" && $Object['message']['text'] == 'مدیریت لیست اعضا' && $is_admin[0]['status']) {
    changeStatus($array, $conn,  $Date, "-1", $Message_id);
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
    $text = $Message_fname . " عزیز در این بخش شما میتوانید اعضای گروه رو مدیریت نمایید";
    startWellcomeinline($Message_id, $text, $Inline_keyboard, $Message_message_id);
    $Keyboard = [["بازگشت به منوی اصلی"]];
    $emoji = ["\xE2\x9B\xB5", "\xE2\x99\xA5", "\xE2\x98\x95", "\xF0\x9F\x8C\x83", "\xF0\x9F\x8C\x8C", "\xF0\x9F\x8D\x9F", "\xF0\x9F\x8D\xAD", "\xF0\x9F\x8D\xB9", "\xF0\x9F\x8E\x89", "\xF0\x9F\x8E\x88"];
    startWellcome($Message_id, $emoji[array_rand($emoji)], $Keyboard, $Message_message_id);
    //
} elseif ($array[0]['status'] == "0" && $Object['message']['text'] == 'مدیریت لیست اعضا' && !$is_admin[0]['status']) {
    sendMessage($Message_id, "شما به این بخش دسترسی ندارید!");
}
// data 


elseif ($array[0]['status'] == "0" && $Object['message']['text'] == 'عضویت در گروه یادآور') {
    //$array = getStatus($conn, $Message_id);
    changeStatus($array, $conn,  $Date, "1", $Message_id);

    $Keyboard = [["بازگشت به منوی اصلی"]];
    startWellcome($Message_id, "لطفا نام کامل خود را وارد نمایید: ", $Keyboard, $Message_message_id);
} elseif ($array[0]['status'] == "1") {

    //if exist update name in database
    try {
        $stmt = $conn->prepare("UPDATE `users` SET `fullname_fa`= ? WHERE `chat_id`= ?");
        $stmt->bindValue(1, $Object['message']['text']);
        $stmt->bindValue(2, $Message_id);
        $stmt->execute();
    } catch (PDOException $e) {
        sendMessage("1178581717",  "<br>" . $e->getMessage());
    }

    $Inline_keyboard = [
        [
            ['text' => "\xE2\x9C\x85", 'callback_data' => "okname-1"],
        ],
        [
            ['text' => "\xE2\x9D\x8C", 'callback_data' => "cancel-1"]
        ]
    ];
    $text = "نام کامل شما : " . $Object['message']['text'] . " است؟";
    startWellcomeinline($Message_id, $text, $Inline_keyboard, $Message_message_id);
} elseif ($Callback_chat_id && $Callback_data && $is_admin[0]['status']) {
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
    // &
    if ($Callback_data[1] == "1") {
        switch ($Callback_data[0]) {
            case "cancel":
                updateStatus($conn,  $Date, "0", $Callback_chat_id);
                $Keyboard = [
                    ['عضویت در گروه یادآور'], ['مدیریت لیست اعضا'], ['درباره']
                ];
                startWellcome($Callback_chat_id, "با سلام به ربات یادآور خوش آمدید.  لطفا یکی از گزینه های زیر را انتخاب نمایید:", $Keyboard, $Callback_message_message_id);
                deleteMessage($Callback_chat_id, $Callback_message_message_id);
                break;
            case "reset":
                updateStatus($conn,  $Date, "-1", $Callback_chat_id);
                $Keyboard = [["بازگشت به منوی اصلی"]];
                startWellcome($Callback_chat_id, "لطفا نام کامل خود را وارد نمایید: ", $Keyboard, $Callback_message_message_id);
                deleteMessage($Callback_chat_id, $Callback_message_message_id);
                break;
            case "okname":
                updateStatus($conn,  $Date, "2", $Callback_chat_id);
                //startWellcomeremove($Callback_chat_id , "\xF0\x9F\x8E\x89");
                $Inline_keyboard = [
                    [
                        ['text' => "1396", 'callback_data' => "1396-1"],
                        ['text' => "1397", 'callback_data' => "1397-1"],
                    ],
                    [
                        ['text' => "1398", 'callback_data' => "1398-1"],
                        ['text' => "1399", 'callback_data' => "1399-1"]
                    ],
                    [
                        ['text' => "1400", 'callback_data' => "1400-1"],
                        ['text' => "1401", 'callback_data' => "1401-1"]
                    ],
                    [
                        ['text' => "1402", 'callback_data' => "1402-1"]
                    ],
                ];
                $text = "لطفا سال ورود خود را به رشته کامپیوتر وارد کنید";
                $re = editMessageReplyMarkup($Callback_chat_id, $Callback_message_message_id, $Inline_keyboard, $text);
                logi($conn, "inline update", $re, $re, $Date);
                answerCallbackQuery($Callback_id, "نام شما تایید شد");
                break;
            case "1396":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1396", $conn);
                break;
            case "1397":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1397", $conn);
                break;
            case "1398":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1398", $conn);
                break;
            case "1399":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1399", $conn);
                break;
            case "1400":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1400", $conn);
                break;
            case "1401":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1401", $conn);
                break;
            case "1402":
                year_inline($Callback_chat_id, $Callback_message_message_id, "1402", $conn);
                break;
            case "0":
                type_inline($Callback_chat_id, $Callback_message_message_id, "0", $conn);
                break;
            case "1":
                type_inline($Callback_chat_id, $Callback_message_message_id, "1", $conn);
                break;
            case "2":
                type_inline($Callback_chat_id, $Callback_message_message_id, "2", $conn);
                break;
            case "3":
                type_inline($Callback_chat_id, $Callback_message_message_id, "3", $conn);
                break;
        }
        if ($Callback_data[0] == "back") {
            $Inline_keyboard = [
                [
                    ['text' => "دانکشده مهندسی", 'callback_data' => "0-1"]
                ],
                [
                    ['text' => "دانشجو پردیس", 'callback_data' => "1-1"]
                ],
                [
                    ['text' => "دانشجو مهمان", 'callback_data' => "2-1"]
                ],
                [
                    ['text' => "دانشجو فارغ التحصیل", 'callback_data' => "3-1"]
                ],
            ];
            $text = "لطفا وضعیت دانشجویی خود را مشخص نمایید: ";
            editMessageReplyMarkup($Callback_chat_id, $Callback_message_message_id, $Inline_keyboard, $text);
        }
        if (in_array($Callback_data[0], ['ok*1402', 'ok*1401', 'ok*1400', 'ok*1399', 'ok*1398', 'ok*1397', 'ok*1396'])) {
            answerCallbackQuery($Callback_id, "سال ورود ذخیره شد");
            $Callback_data[0] = explode('*', $Callback_data[0]);
            try {
                $stmt = $conn->prepare("UPDATE `users` SET `entry_year`= ? WHERE `chat_id`= ?");
                $stmt->bindValue(1, $Callback_data[0][1]);
                $stmt->bindValue(2, $Message_id);
                $stmt->execute();
            } catch (PDOException $e) {
                sendMessage("1178581717",  "<br>" . $e->getMessage());
            }
            $Inline_keyboard = [
                [
                    ['text' => "دانکشده مهندسی", 'callback_data' => "0-1"]
                ],
                [
                    ['text' => "دانشجو پردیس", 'callback_data' => "1-1"]
                ],
                [
                    ['text' => "دانشجو مهمان", 'callback_data' => "2-1"]
                ],
                [
                    ['text' => "دانشجو فارغ التحصیل", 'callback_data' => "3-1"]
                ],
            ];
            $text = "لطفا وضعیت دانشجویی مخاطب را مشخص نمایید: ";
            editMessageReplyMarkup($Callback_chat_id, $Callback_message_message_id, $Inline_keyboard, $text);
        } elseif (in_array($Callback_data[0], ['save*0', 'save*1', 'save*2', 'save*3'])) {
            answerCallbackQuery($Callback_id, "اطلاعات مخاطب ذخیره شد");
            $Callback_data[0] = explode('*', $Callback_data[0]);
            try {
                $stmt = $conn->prepare("UPDATE `users` SET `type`= ? WHERE `chat_id`= ?");
                $stmt->bindValue(1, $Callback_data[0][1]);
                $stmt->bindValue(2, $Message_id);
                $stmt->execute();
                sendMessage("1178581717",  "کاربر با موفقیت افزوده شد");
            } catch (PDOException $e) {
                sendMessage("1178581717",  "<br>" . $e->getMessage());
            }
            deleteMessage($Callback_chat_id, $Callback_message_message_id);
        } elseif (in_array($Callback_data[0], ['0', '1', '2', '3'])) {
        }
    } elseif ($Callback_data[1] == "0") {
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
                $base1 = "";
                $key = 0;
                foreach ($array['result'] as $admins) {
                    $username = $admins["user"]["username"] ?? "";
                    $cutsom_title = $admins["custom_title"] ?? "";
                    if (!$admins["user"]["is_bot"]) {
                        $base1 =  $base1 . "%0A" . ($key + 1) . ". " . $admins["user"]["first_name"] . " @" . $username . " %0A"
                            . "عنوان ادمین در گروه: " . $cutsom_title . "%0A";
                    }
                }

                sendMessage($Callback_chat_id, $base0 . $base1);
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
                // $kir = strval($base0 . $base1);
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

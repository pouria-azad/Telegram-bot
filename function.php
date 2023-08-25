<?php
define('API_TOKEN', '6546866682:AAGJD4uISP2U4RsOU8JC0fWNZ8VirfjGZnE');
define('API_REQUEST', 'https://api.telegram.org/bot' . API_TOKEN . '/');

function sendMessage($Message_id, $text)
{
    $Method = 'sendMessage';
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $Message_id . "&" . "text=" . $text . "&" . "parse_mode=HTML";
    return ($Request_to_server);
}

function startWellcome($Message_id, $text, $keyboard, $Message_message_id)
{
    $Method = 'sendMessage';
    $arr_keyboard = array("keyboard" => $keyboard, "resize_keyboard" => true, "one_time_keyboard" => true);
    $reply_markup = json_encode($arr_keyboard);
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $Message_id . "&" . "text=" . $text . "&" . "reply_markup=" . $reply_markup . "&" . "reply_to_message_id=" . $Message_message_id;
    file_get_contents($Request_to_server);
}

function startWellcomeremove($Message_id, $text)
{
    $Method = 'sendMessage';
    $arr_keyboard = array("remove_keyboard" => true);
    $reply_markup = json_encode($arr_keyboard);
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $Message_id . "&" . "text=" . $text . "&" . "reply_markup=" . $reply_markup;
    return ($Request_to_server);
}

function startWellcomeinline($Message_id, $text, $Inline_keyboard, $Message_message_id)
{
    $Method = 'sendMessage';
    $arr_keyboard = array("inline_keyboard" => $Inline_keyboard, "resize_keyboard" => true, "one_time_keyboard" => true);
    $reply_markup = json_encode($arr_keyboard);
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $Message_id . "&" . "text=" . $text . "&" . "reply_markup=" . $reply_markup . "&" . "reply_to_message_id=" . $Message_message_id;
    file_get_contents($Request_to_server);
}

function getChatMember($chat_id, $user_id)
{
    $Method = 'getChatMember';
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $chat_id . "&" . "user_id=" . $user_id;
    return json_decode(file_get_contents($Request_to_server), true);
}

function answerCallbackQuery($Callback_id, $text)
{
    $Method = 'answerCallbackQuery';
    $Request_to_server = API_REQUEST . $Method . "?" . "callback_query_id=" . $Callback_id . "&" . "text=" . $text;
    file_get_contents($Request_to_server);
}

function year($year, $y, $id)
{
    $base = "\xE2\xAD\x95 \xF0\x9F\x94\xB4 \xE2\xAD\x95 " . "<b><u>ورودی های سال: " . $year . "</u></b>" . " \xE2\xAD\x95 \xF0\x9F\x94\xB4 \xE2\xAD\x95" . "%0A%0A";
    $text = "";
    $text = $text . $base . "1" . ". " . "<a href='tg://user?id=" . $y[0][2] . "'>" . $y[0][0] . "</a>" . " @" . $y[0][1];
    $y99 = array_slice($y, 1);
    foreach ($y99 as $number => $user)
        $text = $text . "%0A" . ($number + 2) . ". " . "<a href='tg://user?id=" . $user[2] . "'>" . $user[0] . "</a>" . " @" . $user[1];
    return sendMessage($id, $text);
}

function type($number, $y, $id)
{
    $t = [
        '1' => "دانشجوهای پردیس",
        '2' => "دانشجوهای مهمان",
        '3' => "دانشجوهای فارغ التحصیل"
    ];
    $base = "\xE2\xAD\x95 \xF0\x9F\x94\xB4 \xE2\xAD\x95 " . "<b><u>" . $t[$number] . "</u></b>" . " \xE2\xAD\x95 \xF0\x9F\x94\xB4 \xE2\xAD\x95" . "%0A%0A";
    $text = "";
    $text = $text . $base . "1" . ". " . "<a href='tg://user?id=" . $y[0][2] . "'>" . $y[0][0] . "</a>" . " @" . $y[0][1];
    $y99 = array_slice($y, 1);
    foreach ($y99 as $number => $user)
        $text = $text . "%0A" . ($number + 2) . ". " . "<a href='tg://user?id=" . $user[2] . "'>" . $user[0] . "</a>" . " @" . $user[1];
    return sendMessage($id, $text);
}

function changeStatus($array, $conn,  $Date, $status, $Message_id)
{
    if (!$array) {
        insertStatus($conn, $Date, $status, $Message_id);
    } else {
        updateStatus($conn,  $Date, $status, $Message_id);
    }
    $conn = null;
}


function insertStatus($conn, $Date, $status, $Message_id)
{
    try {
        $pdo = $conn->prepare("INSERT INTO `status`(`chat_id`, `date` , `status`) VALUES (? , ? , ?)");
        $pdo->bindValue(1, $Message_id);
        $pdo->bindValue(2, $Date);
        $pdo->bindValue(3, $status);
        $pdo->execute();

        // echo "New record created successfully";
    } catch (PDOException $e) {
        //   echo $sql . "<br>" . $e->getMessage();
        sendMessage("1178581717", "<br>" . $e->getMessage());
    }
    $conn = null;
}


function updateStatus($conn,  $Date, $status, $Message_id)
{

    try {

        $stmt = $conn->prepare("UPDATE `status` SET `date`= ? ,`status`= ? WHERE `chat_id`= ?");
        $stmt->bindValue(1, $Date);
        $stmt->bindValue(2, $status);
        $stmt->bindValue(3, $Message_id);
        $stmt->execute();
        // echo a message to say the UPDATE succeeded
        echo $stmt->rowCount() . " records UPDATED successfully";
    } catch (PDOException $e) {
        echo "<br>" . $e->getMessage();
    }
    $conn = null;
}

function logi($conn, $name, $text_log, $json_log, $date)
{
    $pdo = $conn->prepare("INSERT INTO `kj`(`name` , `text` , `log` , `date`) VALUES (? , ? , ? , ?)");
    $pdo->bindValue(1, $name);
    $pdo->bindValue(2, $text_log);
    $pdo->bindValue(3, $json_log);
    $pdo->bindValue(4, $date);
    $pdo->execute();

    $conn = null;
}

function getStatus($conn, $Message_id)
{
    try {
        $pdo = $conn->prepare("SELECT `chat_id`,`status` FROM `status` WHERE `chat_id`= ? LIMIT 1");
        $pdo->bindValue(1, $Message_id);
        $pdo->execute();
        return $pdo->fetchAll();
    } catch (PDOException $e) {
        echo  "<br>" . $e->getMessage();
    }
}

function getChatAdministrators($chat_id)
{
    $Method = 'getChatAdministrators';
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . "$chat_id";
    return json_decode(file_get_contents($Request_to_server), true);
}

function editMessageReplyMarkup($chat_id, $Message_id, $Inline_keyboard, $text)
{
    editMessageText($chat_id, $Message_id, $text);
    $Method = 'editMessageReplyMarkup';
    $arr_keyboard = array("inline_keyboard" => $Inline_keyboard, "resize_keyboard" => true, "one_time_keyboard" => true);
    $reply_markup = json_encode($arr_keyboard);
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $chat_id . "&" . "message_id=" . $Message_id . "&" . "reply_markup=" . $reply_markup;
    return file_get_contents($Request_to_server);
}
function editMessageText($chat_id, $message_id, $text)
{
    $Method = 'editMessageText';
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $chat_id . "&" . "message_id=" . $message_id . "&" . "text=" . $text;
    return file_get_contents($Request_to_server);
}
function deleteMessage($chat_id, $message_id)
{
    $Method = 'deleteMessage';
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $chat_id . "&" . "message_id=" . $message_id;
    return file_get_contents($Request_to_server);
}

function year_inline($Callback_chat_id, $Callback_message_message_id, $year, $conn)
{
    try {
        $stmt = $conn->prepare("UPDATE `temp_user` SET `entry_year`= ? WHERE `id`= ?");
        $stmt->bindValue(1, $year);
        $stmt->bindValue(2, $Callback_chat_id);
        $stmt->execute();
    } catch (PDOException $e) {
        sendMessage("1178581717",  "<br>" . $e->getMessage());
    }
    $conn = null;
    $Inline_keyboard = [
        [
            ['text' => "\xE2\x86\xA9", 'callback_data' => "okname-1"],
            ['text' => "\xE2\x9C\x85", 'callback_data' => "ok*" . $year . "-1"],
        ],
        [
            ['text' => "\xF0\x9F\x94\x99", 'callback_data' => "reset-1"],
        ]

    ];
    $text = "سال ورود مخاطب شما " . $year . " است؟";
    editMessageReplyMarkup($Callback_chat_id, $Callback_message_message_id, $Inline_keyboard, $text);
}

function type_inline($Callback_chat_id, $Callback_message_message_id, $type = "0", $conn)
{
    try {
        $stmt = $conn->prepare("UPDATE `temp_user` SET `type`= ? WHERE `id`= ?");
        $stmt->bindValue(1, $type);
        $stmt->bindValue(2, $Callback_chat_id);
        $stmt->execute();
    } catch (PDOException $e) {
        sendMessage("1178581717",  "<br>" . $e->getMessage());
    }
    $conn = null;
    $Inline_keyboard = [
        [
            ['text' => "\xE2\x86\xA9", 'callback_data' => "back-1"],
            ['text' => "\xE2\x9C\x85", 'callback_data' => "save*" . $type . "-1"],
        ],
        [
            ['text' => "\xF0\x9F\x94\x99", 'callback_data' => "okname-1"]
        ]

    ];
    $t = [
        '0' => "دانکشده مهندسی",
        '1' => "دانشجو پردیس",
        '2' => "دانشجو مهمان",
        '3' => "دانشجو فارغ التحصیل"
    ];
    $text = "وضعیت مخاطب شما " . $t[$type] . " است؟";
    editMessageReplyMarkup($Callback_chat_id, $Callback_message_message_id, $Inline_keyboard, $text);
}

<?php
define('API_TOKEN', '6546866682:AAGJD4uISP2U4RsOU8JC0fWNZ8VirfjGZnE');
//define('API_TOKEN', '6550970307:AAFBcBSWJmEYgXpse7cs-ckQ7cPRmZEX-6k');
define('API_REQUEST', 'https://api.telegram.org/bot' . API_TOKEN . '/');

function sendMessage($Message_id, $text)
{
    $Method = 'sendMessage';
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $Message_id . "&" . "text=" . $text;
    return file_get_contents($Request_to_server);
}

function startWellcome($Message_id, $text, $keyboard, $Message_message_id)
{
    $Method = 'sendMessage';
    $arr_keyboard = array("keyboard" => $keyboard, "resize_keyboard" => true, "one_time_keyboard" => true);
    $reply_markup = json_encode($arr_keyboard);
    $Request_to_server = API_REQUEST . $Method . "?" . "chat_id=" . $Message_id . "&" . "text=" . $text . "&" . "reply_markup=" . $reply_markup . "&" . "reply_to_message_id=" . $Message_message_id;
    file_get_contents($Request_to_server);
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
    $text = $text . $base . "1" . " " . $y[0][0] . " @" . $y[0][1];
    $y99 = array_slice($y, 1);
    foreach ($y99 as $number => $user)
        $text = $text . "%0A" . ($number + 2) . ". " . $user[0] . " @" . $user[1];
    sendMessage($id, $text);
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
        sendMessage("1178581717", "New record created successfully");
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



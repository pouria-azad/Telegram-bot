<?php
include './function.php';
include "./config.php";

$Message_id = 1178581717;

sendMessage("1178581717", "666");


    $sql = "INSERT INTO `status`(`chat_id`, `status`) VALUES ('1178581717' , '0')";
    sendMessage("1178581717", "1");
    $stml = $conn->prepare($sql);
    $stmt->execute();
    $publisher_id = $conn->lastInsertId();
    sendMessage("1178581717", 'The publisher id ' . $publisher_id . ' was inserted');
    $Keyboard = [ ['dfgdدرباره']];
    startWellcome($Message_id, "با سلام به ربات یادآور خوش آمدید.\nلطفا یکی از گزینه های زیر را انتخاب نمایید:", $Keyboard, $Message_message_id);
}
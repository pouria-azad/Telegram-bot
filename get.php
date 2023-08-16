<?php
include './function.php';
$Content = file_get_contents('php://input');
$Object = json_decode($Content , true);
$Chat_id = $Object->message->chat->id;

sendMessage($Chat_id, 'hi');
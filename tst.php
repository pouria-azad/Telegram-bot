<?php
include "./config.php";
include './function.php';
// $sql = "INSERT INTO `users` (`chat_id`, `username`, `entry_year`, `fullname`, `status`) VALUES (?, NULL, NULL, NULL, NULL);";
// $stml = $conn->prepare($sql);
// $chat_id = 123456789;
// $stmt->bind_param($chat_id);
// echo $stmt->execute();

$sql = "SELECT `chat_id`, `username` FROM  `users`";
$stmt = $conn->prepare($sql); 
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$array = $stmt->fetchAll();
var_dump($array);
echo "\n\n";
foreach($array as $users){
    if(!$users['username']){
        $status = getChatMember("@site132ddd" , $users['chat_id']);
        if ( $status['ok'] ){
            var_dump($status['result']['user']['username']);
            echo "\n";
    }}
}



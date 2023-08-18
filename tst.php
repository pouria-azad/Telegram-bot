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



foreach ($array as $users) {
    // if (!$users['username']) {
        $status = getChatMember(-1001454096414, $users['chat_id']);
        if ($status['ok'] && $status['result']['user']['username']) {
            try {
                $sql = "UPDATE `users` SET `username`= ? WHERE `chat_id`= ?";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $status['result']['user']['username']);
                $stmt->bindValue(2, $users['chat_id']);
                $stmt->execute();
                echo $stmt->rowCount() . " records UPDATED successfully";
            } catch (PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }
    }
// }

<?php
include "./config.php";
$sql = "INSERT INTO `users` (`chat_id`, `username`, `entry_year`, `fullname`, `status`) VALUES (?, NULL, NULL, NULL, NULL);";
$stml = $conn->prepare($sql);
$chat_id = 123456789;
$stmt->bind_param($chat_id);
echo $stmt->execute();
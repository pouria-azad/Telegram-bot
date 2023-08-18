<?php
include "./config.php";
$sql = "INSERT INTO `users`(`chat_id`) VALUES (?)";
$stml = $conn->prepare($sql);
$stmt->bind_param(123456789);
echo $stmt->execute();
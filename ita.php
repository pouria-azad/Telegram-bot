<?php
//include "./function.php";
include "./config.php";

$Content = file_get_contents('php://input');
$Object = json_decode($Content, true);
$Message_id = $Object['message']['from']['id'];

try {
    $sql = "INSERT INTO `status`(`chat_id`, `status`) VALUES (? , ?)";
    $stml = $conn->prepare($sql);
    $stmt->bindValue(1, $Message_id);
    $stmt->bindValue(2, "0");
    // use exec() because no results are returned
    $conn->exec($sql);
    //sendMessage("1178581717", "sec");
} catch (PDOException $e) {
    //sendMessage("1178581717", "failse<br>" . $e->getMessage());
}
$conn = null;



// try {
//     $sql = "INSERT INTO `status`(`chat_id`, `status`) VALUES ('1178581717' , '0')";
//     // use exec() because no results are returned
//     $conn->exec($sql);
//     echo "New record created successfully";
//   } catch(PDOException $e) {
//     echo $sql . "<br>" . $e->getMessage();
//   }
  
//   $conn = null;
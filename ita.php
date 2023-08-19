<?php
//include "./function.php";
include "./config.php";


$Message_id = "111";

try {
    $sql = "INSERT INTO `kj` (`id`) VALUES (:chat_id);";
    $stml = $conn->prepare($sql);
    $stmt->bindValue(":chat_id", $Message_id);
    //$stmt->bindValue(":status", "0");
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "New record created successfully";
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
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
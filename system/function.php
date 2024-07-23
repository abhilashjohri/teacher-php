<?php 
 function checkloginSession(){

    if (!isset($_SESSION['teacher_id'])) {
        header('Location: login.php');
        exit();
    }
 }
?>
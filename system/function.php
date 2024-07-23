<?php 
 function checkloginSession(){

    if (!isset($_SESSION['teacher_id'])) {
        header('Location: login.php');
        exit();
    }
 }

 function is_admin(){
     if($_SESSION['role']=='Admin'){
         return true;
        }else {
            return false;
        }
     }
 
?>
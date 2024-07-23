<?php
include_once 'system/autoload.php';
$db = new Database();
$conn = $db->connect();
$module = isset($_REQUEST['module'])?$_REQUEST['module']:'';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

 // teacher module 
if( $module =='teacher'){
	include_once 'modules/Teacher.php';
	$teacher = new Teacher($conn);
	switch ($action) {
		case 'login':
			$email = $_POST['email'];
            $password = $_POST['password'];
            $teacher->login($email, $password);
            break;
		case 'register':
			$name = $_POST['name'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$resumeFile = $_FILES['resume'];
			$imageFile = $_FILES['image'];
			$teacher->register($name, $email, $password, $resumeFile, $imageFile);
			break;
				
		  case 'logout':
			$teacher->logout();
		  break;  
		case 'listTeacher':
			$teacher->listTeacher();
		  break;
		case 'getByID':
			$teacher->teacher_id = $_POST["id"];
	       echo  $teacher->getByID();
		  break;
		  case 'addteacher':
			$teacher->addteacher();
		  break; 
		  case 'updateteacher':
			// echo 33;
			$teacher->updateteacher();
		  break;  
		
		default:
	  }
}

?>
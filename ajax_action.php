<?php
include_once 'system/autoload.php';
$db = new Database();
$conn = $db->connect();
$module = isset($_REQUEST['module'])?$_REQUEST['module']:'';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

 // teacher module 
if( $module =='teacher'){
	include_once 'modules/Teacher.php';
	$teacher = new Teacher($db);
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
			$id = $_POST["id"];
	       echo  $teacher->getByID($id);
		  break; 
		  case 'get_teacher':
			$id = $_POST["id"];
		   echo json_encode($teacher->getByID($_POST['id']));
		  break; 
		  case 'saveTeacher':
			$teacher->saveTeacher($_POST, $_FILES);
		  break; 
		  case 'update_status':
			$teacher->updateStatus($_POST['id'], $_POST['status']);
		  break; 
		  case 'delete_teacher':
			$teacher->deleteTeacher($_POST['id']);
		  break;  
		
		default:
	  }
}


?>
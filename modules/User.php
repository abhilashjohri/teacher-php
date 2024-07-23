<?php
class User {	
	private $table = 'users';
	private $conn;
	private $db;
	public $teacher_id; 
	public function __construct($db){
        $this->conn = $db;
		$this->db = new Database();
    }	  
	

	//`user_id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `role`, `department`, `designation`, `created_at`, `updated_at`, `user_name`, 
	public function login(){
		 $email =  $_POST['email'];
	   $password =  $_POST['password']; 
	     $sqlQuery = "SELECT * FROM ".$this->table." WHERE email = ?";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("s", $email);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
		   //  print_r($record);
             if(!$record){
				echo json_encode(['status'=>'error', 'msg'=>"No record found"]);
		       	return; 
			 }
			else if (password_verify($password, $record['password'])){ 
	
				if($record['status'] =="Inactive"){
						echo json_encode(['status'=>'error', 'msg'=>"Your account is Inactive please contact to admin"]);
						return; 
					}else{
						if (session_status() === PHP_SESSION_NONE) {
							session_start();
						}
						$_SESSION['loggedin'] = true;
						$_SESSION['user_id'] = $record['user_id'];
						$_SESSION['email'] = $record['email'];
						$_SESSION['name'] = $record['first_name'].' '.$record['last_name'];
						$_SESSION['user_type'] = $record['user_type'];
						$_SESSION['status'] = $record['status'];
						echo json_encode(['status'=>'success', 'msg'=>"Login Successfully"]);
						return; 
				   }
            } 
            else{
				echo json_encode(['status'=>'error', 'msg'=>"Invalid Credentials"]);
		       	return; 
            }
    }	 
	public function recoverPassword(){
		$newpassword = $_POST['newpassword'];
		$confirmpassword = $_POST['confirmpassword'];
		$token = $_POST['token'];
        if(!$token ){
			echo json_encode(['status'=>'error', 'msg'=>"Invalid"]);
			return; 
		}else if($newpassword !== $confirmpassword){
			echo json_encode(['status'=>'error', 'msg'=>"Your new password and confirm password is not same "]);
			return; 
		}else{
			$sqlQuery = "SELECT * FROM ".$this->table." WHERE reset_password_token = ?";	
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("s", $token);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
			if(!$record){
				echo json_encode(['status'=>'error', 'msg'=>"No record found or your reset  link is expired"]);
		       	return; 
			 }else{
				$pwd_credit =base64_encode($newpassword);
				$newpassword = password_hash($newpassword, PASSWORD_DEFAULT);
				$ud_data = array( 'user_id'=> $record['user_id'],'reset_password_token'=>'','password'=>$newpassword,'pwd_credit'=>$pwd_credit,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$record['user_id']);
				$this->db->update('users', $ud_data,'user_id');
				echo json_encode(['status'=>'success', 'msg'=>"your password reset successfully"]);
				return; 
			 }

		}


	 }
	public function forgetPassword(){
			$email = $_POST['email'];
		$sqlQuery = "SELECT * FROM ".$this->table." WHERE email = ?";	
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $email);	
		$stmt->execute();
		$result = $stmt->get_result();
		$record = $result->fetch_assoc();
		if(!$record){
			echo json_encode(['status'=>'error', 'msg'=>"No record found"]);
			   return; 
		 }else{
            $token =  $record['user_id'].date("Ymdh");
			 $token = password_hash($token, PASSWORD_DEFAULT);
			$ud_data = array( 'user_id'=> $record['user_id'],'reset_password_token'=>$token);
			$this->db->update('users', $ud_data,'user_id');
			//$email = 'monoabhilash@gmail.com';
			$email =  $record['email'];
			$subject = "HMS Monoinfotech - Reset password";
			$reset_link =SITE_URL."/recover-password.php?token=".$token;
			$user_name = $record['first_name'].' '.$record['last_name'];
			$content = "<p>Dear <b>$user_name</b>,</p>
			<p>Forgot your password?<br>
			We received a request to reset the password for your account</p>
			
			<p>To reset your password ,click on ther button below<br>
			<button type='button'><a href='$reset_link'>Reset password</a></button></p>
			<p>Or copy and paste the URL into your browser<br><a href='$reset_link'>$reset_link</a></p>";
			$res = send_email($email,$subject,$content);
			 if($res['status']==200){
				echo json_encode(['status'=>'success', 'msg'=>"Reset password link mail send to successfully"]);
				return; 
			 }else {
			echo json_encode(['status'=>'error', 'msg'=>"mail not send"]);
			return; 
			 }

		 }
	 }  
	 
	public function logout(){
		session_start();
		session_destroy(); //destroy the session
		unset($_SESSION["email"]);
	 }	 

	 public function changePassword(){
		$this->user_id = $_SESSION['user_id'];
		if($this->user_id){
		$currentPassword = $_POST['currentpassword'];
		$newPassword = $_POST['newpassword'];
		$confirmPassword = $_POST['confirmpassword'];
		$sqlQuery = "select * from ".$this->table." where user_id = ".$this->user_id;
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$records = $stmt->get_result();
		if($records->num_rows > 0){
			$details = $records->fetch_assoc();
			// $data = print_r($details);
			$originalPassword = $details["password"];
			if(password_verify($currentPassword, $originalPassword)){
				if($newPassword == $confirmPassword){
				$newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
				$sqlQuery = "update ".$this->table." set password=? where user_id=?";
				$stmt = $this->conn->prepare($sqlQuery);
				$stmt->bind_param('si', $newPassword, $this->user_id) ;
				if($stmt->execute()){
					echo json_encode(['status'=>'success', 'msg'=>"Password Updated Successfully"]);
					return;
				}
			}else{
				echo json_encode(["status" => "error" , 'msg' => 'New password and Confirm password do not match']);
				return;	
			}
		}else{
				echo json_encode(["status" => "error", 'msg' => "Please insert correct password"]);
				return;
			}
			// return $data;
		}else{
			echo json_encode(["status" => "error", 'msg' => "Record not found"]);
				return;	
		}
	 }
	}
	
	 public function profile(){
		
	 }
	 public function lockScreen(){
		
	 }

	 public function listUsers(){
		//`user_id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `role`, `department`, `designation`, `created_at`, `updated_at`, `user_name`, 
		$orderByArr=[
           0=>'user_id',
		   1=>'first_name',
		   2=>'email',
		   3=>'user_type',
		   4=>'status',
		   5=>'created_at'

		];
		$sqlQuery = "SELECT * FROM ".$this->table." where 1  ";
		$pro_con =' and ';
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' and (user_id LIKE "%'.trim($_POST["search"]["value"]).'%" ';
			$sqlQuery .= ' OR email LIKE "%'.trim($_POST["search"]["value"]).'%" ';			
			$sqlQuery .= ' OR first_name LIKE "%'.trim($_POST["search"]["value"]).'%" ';
			$sqlQuery .= ' OR last_name LIKE "%'.trim($_POST["search"]["value"]).'%" ';
			$sqlQuery .= ' OR user_type LIKE "%'.trim($_POST["search"]["value"]).'%" ';
			$sqlQuery .= ' OR status LIKE "%'.trim($_POST["search"]["value"]).'%") ';	
			$pro_con =" and ";				
		}
		## Custom Field value start
		$status_filter = $_POST['status_filter'];
		$type_filter = $_POST['type_filter'];
		
		## Search 
		if($status_filter != ''){
			$sqlQuery .= $pro_con.' users.`status`="'.$status_filter.'" ';
		} 
		if($type_filter != ''){
			$sqlQuery .= $pro_con.' users.`user_type`="'.$type_filter.'" ';
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$orderByArr[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY status asc,user_id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery_limit = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
	     	// echo $sqlQuery;
		$stmt = $this->conn->prepare($sqlQuery.$sqlQuery_limit);
		$stmt->execute();
		$result = $stmt->get_result();	
		$stmtTotal = $this->conn->prepare("SELECT * FROM ".$this->table);
		$stmtTotal->execute();
		$allResult = $stmtTotal->get_result();
		$allUsers = $allResult->num_rows;
		
		## Total number of record with filtering
		$stmt2 = $this->conn->prepare($sqlQuery); 
		$stmt2->execute();
		$resultfilter = $stmt2->get_result();	
		$displayUsers = $resultfilter->num_rows;
	
		$records = array();		
		$i=$_POST['start']+1;
		while ($record = $result->fetch_assoc()) { 	
			$rows = array();	
		
                // Status class  
			if($record['status'] =='Active'){
				$st_class ='success';  
			}else if($record['status'] =='Inactive'){
				$st_class ='danger';
				$sync_btn ="disabled";
			}else {
	           	$st_class ='warning'; 
			}
           // action
		   $action ='';
	    	//	$action = '<button data-toggle="tooltip" title="view" type="button" name="view"  id= "'.$record["user_id"].'" class="btn btn-primary  fa fa-eye btn-sm view mr-2"></button>';
			$action .= '<button data-toggle="tooltip" title="Edit"  type="button" name="update"  id= "'.$record["user_id"].'" class=" fas fa-edit btn btn-warning btn-sm update mr-2"></button>';
			$action .= '<button data-toggle="tooltip" title="Delete"  type="button" name="delete"  id= "'.$record["user_id"].'" class=" btn btn-danger btn-sm delete mr-2" ><i class="fas fa-trash"></i></button>';
			 
		
			$checkbox = '<input type ="checkbox" class="dtcheckbox " name="checked['.$i.']"   value="'.$record["user_id"].'"></input>';
			//$rows[] = $checkbox;
			//$rows[] = $record['user_id'];
			$name_view = '<a  name="view" data-id="'.$record["user_id"].'" class="view">'.$record['first_name'].' '.$record['last_name'].'</a>';
			$rows[] = $i;
			$rows[] = $name_view ;	  
			$rows[] = $record['email'];	
			$rows[] = ucfirst($record['user_type']);	
			 $rows[] = '<span class="badge badge-'.$st_class.'">'.$record['status'].'</span>';
		    $rows[] = $record['created_at'];
			 $rows[] = $action;
		
			//$rows[] = $record['updated_at'];		
			$records[] = $rows;
			$i++;
		}
		$output = array(
			"draw"	=>	intval($_POST["draw"]),		
			"query"	=>	$sqlQuery,				
			"iTotalRecords"	=>  $allUsers,	
			"iTotalDisplayRecords"	=>  $displayUsers,
			"data"	=> 	$records
		);
		echo json_encode($output);
	}
	public function getUser(){
		if($this->user_id) {
			$sqlQuery = "SELECT `user_id`, `first_name`, `last_name`, `email`,  `user_type`, `status`, `created_at`, `updated_at` FROM ".$this->table." WHERE user_id = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->user_id);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
			return json_encode($record);
		}
	}
	public function user_lists(){
		$status ='Active';
		$sqlQuery = "SELECT user_id,first_name,last_name,user_type FROM ".$this->table." WHERE status = ?";			
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $status);	
		$stmt->execute();
		$resultSet = $stmt->get_result();
		$result = $resultSet->fetch_all(MYSQLI_ASSOC);
		 return $result;
		//echo json_encode($record);
      }
	public function check_duplicate_count(){
        $email = htmlspecialchars(strip_tags($_POST["email"]));
		if($email) {
			$sqlQuery = "
				SELECT * FROM ".$this->table." 
				WHERE  email = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("s", $email);	
			$stmt->execute();
			$result = $stmt->get_result();
			return $allUsers = $result->num_rows;
		}
	} 
	public function updateUser(){
		 $this->user_id =(isset($_POST['id']))?htmlspecialchars(strip_tags($_POST["id"])):"";
		if($this->user_id) {			
	
           //  print_R($_POST);
            $first_name = htmlspecialchars(strip_tags($_POST["first_name"]));
			$last_name = htmlspecialchars(strip_tags($_POST["last_name"]));
			$status = htmlspecialchars(strip_tags($_POST["status"]));
            $user_type = htmlspecialchars(strip_tags($_POST["user_type"]));
             $updated_at = date("Y-m-d H:i:s");
             $updated_by = @$_SESSION['user_id'];
			 $password = trim($_POST["password"]);
			 $pwd_credit =base64_encode($password);

			 if($password){
				$password = password_hash($password, PASSWORD_DEFAULT);
				$stmt = $this->conn->prepare("UPDATE ".$this->table." SET first_name= ?,last_name= ?,user_type=?,status=?,updated_by=?,updated_at=?,password=? ,pwd_credit=? WHERE user_id = ?");
				$stmt->bind_param("ssssssssi", $first_name,$last_name,$user_type,$status,$updated_by,$updated_at,$password,$pwd_credit,$this->user_id);
				$msg = "User Updated  and  change password Successfully ";
			 }else{
				$stmt = $this->conn->prepare("UPDATE ".$this->table." SET first_name= ?,last_name= ?,user_type=?,status=?,updated_by=?,updated_at=? WHERE user_id = ?");
				$stmt->bind_param("ssssssi", $first_name,$last_name,$user_type,$status,$updated_by,$updated_at,$this->user_id);
				$msg = "User Updated Successfully";
			 }

			if($stmt->execute()){
				echo json_encode(['status'=>'success', 'msg'=>$msg  ]);
				return; 
			 
			}else {
				echo json_encode(['status'=>'error', 'msg'=>"User not Updated" ]);return; 
			}
		}	 
	}
	public function addUser(){
		if($_POST) {
            $first_name = htmlspecialchars(strip_tags($_POST["first_name"]));
			$last_name = htmlspecialchars(strip_tags($_POST["last_name"]));
            $email = htmlspecialchars(strip_tags($_POST["email"]));
			$password = trim($_POST["password"]);
			$pwd_credit =base64_encode($password);
			$password = password_hash($password, PASSWORD_DEFAULT);
            $status = htmlspecialchars(strip_tags($_POST["status"]));
            $user_type = htmlspecialchars(strip_tags($_POST["user_type"]));
             $created_at = date("Y-m-d H:i:s");
             $updated_by = @$_SESSION['user_id'];
			
			if($this->check_duplicate_count()==0){
				    $data_arr =[ 'first_name' =>$first_name,
					'last_name' =>$last_name,
					 'email' =>$email,
					 'password' =>$password,
					 'pwd_credit' =>$pwd_credit,
					 'status' =>$status,
					 'user_type' =>$user_type,
					 'created_at' =>$created_at,
					 'updated_by' =>$updated_by
					];
					 $res =  $this->db->insert($this->table,$data_arr);
					// $stmt = $this->conn->prepare("INSERT INTO ".$this->table."(`first_name`, `last_name`, `email`, `password`, `status`, `user_type`, `created_at`,`updated_by`)
					//   VALUES(?,?,?,?,?,?,?,?)");
					//  $stmt->bind_param("ssssssss", $first_name,$last_name,$email,$password,$status,$user_type,$created_at,$updated_by);
					//  if($stmt->execute()){
						if($res){	
						echo json_encode(['status'=>'success', 'msg'=>"User add Successfully"]);
		            	return; 
					}else {
						echo json_encode(['status'=>'error', 'msg'=>"Please try again later"]);
		            	return; 
					}	
		   }else {
			      //duplicate
				  echo json_encode(['status'=>'info', 'msg'=>"This account details is already exists "]);
				  return; 
		     }
		}
	}
	public function deleteUser(){
		if($this->user_id) {			
			$stmt = $this->conn->prepare(" DELETE FROM ".$this->table." WHERE user_id = ?");
			$this->user_id = htmlspecialchars(strip_tags($this->user_id));
			$stmt->bind_param("i", $this->user_id);
			if($stmt->execute()){
				//return true;
				echo json_encode(['status'=>'success', 'msg'=>"User Deleted Successfully"]);
				return; 
			}
		}
	}
}

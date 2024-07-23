<?php
class Teacher {

    private $conn;
    private $db;
    private  $teacher_id;
    private $table_name = "teachers";

    public function __construct($db) {
        $this->conn = $db->connect();
        $this->db = $db;
    }

    public function login($email, $password) {
        $query = 'SELECT * FROM teachers WHERE email = :email';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teacher && password_verify($password, $teacher['password'])) {
            session_start();
            $_SESSION['teacher_id'] = $teacher['id'];
            $_SESSION['teacher_name'] = $teacher['name'];
            $_SESSION['status'] = $teacher['status'];
            $_SESSION['role'] = $teacher['role'];
            echo '<div class="alert alert-success">Login successful! Redirecting...</div>';
        } else {
            echo '<div class="alert alert-danger">Login failed. Invalid email or password.</div>';
        }
    }


    public function register($name, $email, $password, $resumeFile, $imageFile) {
        // Check if the email already exists
        $checkQuery = 'SELECT * FROM teachers WHERE email = :email';
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo '<div class="alert alert-danger">Email already exists. Please use a different email.</div>';
            return;
        }

        // Allowed file types and max file size
        $allowedResumeTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 10485760; // 10MB

        if (in_array($resumeFile['type'], $allowedResumeTypes) && in_array($imageFile['type'], $allowedImageTypes)) {
            if ($resumeFile['size'] <= $maxFileSize && $imageFile['size'] <= $maxFileSize) {
                $resumeNewName = uniqid('', true) . "." . pathinfo($resumeFile['name'], PATHINFO_EXTENSION);
                $imageNewName = uniqid('', true) . "." . pathinfo($imageFile['name'], PATHINFO_EXTENSION);
                $resumeDestination = 'uploads/resumes/' . $resumeNewName;
                $imageDestination = 'uploads/images/' . $imageNewName;

                if (move_uploaded_file($resumeFile['tmp_name'], $resumeDestination) && move_uploaded_file($imageFile['tmp_name'], $imageDestination)) {
                    $query = 'INSERT INTO teachers (name, email, password, resume, image) VALUES (:name, :email, :password, :resume, :image)';
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
                    $stmt->bindParam(':resume', $resumeNewName);
                    $stmt->bindParam(':image', $imageNewName);

                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success">Registration successful!</div>';
                    } else {
                        echo '<div class="alert alert-danger">Failed to register.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Error uploading files.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">File size exceeds 10MB.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Invalid file format.</div>';
        }
    }

    public function logout() {

    }
    public function listTeacher() {
            $request = $_REQUEST;
    
            $columns = [
                0 => 'id',
                1 => 'name',
                2 => 'email',
                3 => 'resume',
                4 => 'image'
            ];
    
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $totalData = $stmt->rowCount();
            $totalFiltered = $totalData;
    
            $limit = $request['length'];
            $start = $request['start'];
            $order = $columns[$request['order'][0]['column']];
            $dir = $request['order'][0]['dir'];
    
            if (!empty($request['search']['value'])) {
                $search = $request['search']['value'];
                $query .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
            }
    
            $query .= " ORDER BY $order $dir LIMIT $start, $limit";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nestedData = [];
                $nestedData['id'] = $row['id'];
                $nestedData['name'] = $row['name'];
                $nestedData['email'] = $row['email'];
                // $nestedData['resume'] = $row['resume'];
                // $nestedData['image'] = $row['email'];
                $nestedData['resume'] = '<a href="../uploads/resumes/'.$row["resume"].'" target="_blank">View Resume</a>';
                $nestedData['image'] = '<img src="../uploads/images/'.$row['image'].'" width="50" height="50">';
                $nestedData['status'] = $row['status'];
                $nestedData['actions'] = '
                    <button class="btn btn-primary btn-sm edit-btn" data-id="'.$row['id'].'">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="'.$row['id'].'">Delete</button>
                    <button class="btn btn-secondary btn-sm status-btn" data-id="'.$row['id'].'" data-status="'.$row['status'].'">'.($row['status'] == "Active" ? 'Deactivate' : 'Activate').'</button>
                ';
    
                $data[] = $nestedData;
            }
    
            $json_data = [
                "draw" => intval($request['draw']),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            ];
    
            echo json_encode($json_data);
        
    }
    public function saveTeacher($post, $files) {
        
        $error_arr = [];
        $id = $post['id'];
        $data =array();
        $data['name']  = $post['name'];
        $data['email'] =$email = $post['email'];

        $checkQuery = 'SELECT * FROM teachers WHERE email = :email';
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0 && $id <1) {
            $error_arr[]= 'Email already exists. Please use a different email';
        }
         if($post['password']){
            $data['password'] =  password_hash($post['password'], PASSWORD_DEFAULT);
         }

         $allowedResumeTypes = [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 10485760; // 10MB
    

    
         if(isset($files['resume']) && $files['resume']['error'] == 0) {
            if (!in_array($files['resume']['type'], $allowedResumeTypes)) {
                $error_arr[] = 'Invalid resume file type. Only PDF, DOC, and DOCX are allowed.';

            }elseif ($files['resume']['size'] > $maxFileSize) {
                $error_arr[] = 'Image file size exceeds 10MB limit.';
            }else {
            $data['resume'] = $this->uploadFile($files['resume'], 'resumes');
            }
        }
    
        if(isset($files['image']) && $files['image']['error'] == 0) {

            if (!in_array($files['image']['type'], $allowedImageTypes)) {
                $error_arr[] = 'Invalid image file type. Only JPG, JPEG, and PNG are allowed.'; 
            } elseif ($files['image']['size'] > $maxFileSize) {
                    $error_arr []= 'Image file size exceeds 10MB limit.';
                  
            }else {
            $data['image'] = $this->uploadFile($files['image'], 'images');
            }
        }
        $condition = [ 
            'id' => $id
        ];
         if(count($error_arr))   {
            echo json_encode(['status'=>"error",'msg'=>implode(", ",$error_arr)]); 
         }  else {
        if ($id) {
            // Update teacher
            if($this->db->update($this->table_name, $data, $condition)){
               echo json_encode(['status'=>"success",'msg'=>"update successfully"]);
            }else {
                echo json_encode(['status'=>"error",'msg'=>"Not updated "]); 
            }
        } else {
            // Insert new teacher
            if($this->db->insert($this->table_name, $data)){
               echo json_encode(['status'=>"success",'msg'=>"Insert successfully"]);
            }else {
              echo json_encode(['status'=>"error",'msg'=>"Not insert"]);    
            }
        }
    }
    }
 
    public function deleteTeacher($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

    }

    public function updateStatus($id) {
        // Your logic to update the status of a teacher
        // For example, toggling the status between active and inactive
        $query = "UPDATE " . $this->table_name . " SET status = IF(status='active', 'inactive', 'active') WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
    }

    private function uploadFile($file, $type) {
        $filename = uniqid('', true) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
        $targetDir = "uploads/$type/";
        $targetFile = $targetDir . $filename;
        move_uploaded_file($file["tmp_name"], $targetFile);
        return $filename;
 
    }
      
       
    public function getTeacher($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByID($id) {

        $query = 'SELECT * FROM teachers WHERE id = :id';
        $stmt =$this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
       return $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    }
    
 

}

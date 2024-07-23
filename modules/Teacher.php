<?php
class Teacher {

    private $conn;
    private  $teacher_id;

    public function __construct($db) {
        $this->conn = $db;
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

    }

    public function profile() {

    }
    public function getByID($id) {

        $query = 'SELECT * FROM teachers WHERE id = :id';
        $stmt =$this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
       return $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    }
    public function addteacher() {

    }
    public function updateteacher() {

    }
    public function deleteteacher() {

    }



}

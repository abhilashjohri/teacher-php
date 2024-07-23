<?php
include 'Database.php';
$db = new Database();
$conn = $db->connect();
$action ="";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // File upload handling
    $resume = $_FILES['resume']['name'];
    $resumeTmpName = $_FILES['resume']['tmp_name'];
    $resumeSize = $_FILES['resume']['size'];
    $resumeError = $_FILES['resume']['error'];
    $resumeType = $_FILES['resume']['type'];

    $image = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];
    $imageError = $_FILES['image']['error'];
    $imageType = $_FILES['image']['type'];

    $resumeExt = explode('.', $resume);
    $resumeActualExt = strtolower(end($resumeExt));
    $allowedResume = array('pdf', 'doc', 'docx');

    $imageExt = explode('.', $image);
    $imageActualExt = strtolower(end($imageExt));
    $allowedImage = array('jpg', 'jpeg', 'png');

    if (in_array($resumeActualExt, $allowedResume) && in_array($imageActualExt, $allowedImage)) {
        if ($resumeError === 0 && $imageError === 0) {
            if ($resumeSize <= 10485760 && $imageSize <= 10485760) {
                $resumeNewName = uniqid('', true) . "." . $resumeActualExt;
                $imageNewName = uniqid('', true) . "." . $imageActualExt;
                $resumeDestination = 'uploads/resumes/' . $resumeNewName;
                $imageDestination = 'uploads/images/' . $imageNewName;
                move_uploaded_file($resumeTmpName, $resumeDestination);
                move_uploaded_file($imageTmpName, $imageDestination);

                $data = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'resume' => $resumeNewName,
                    'image' => $imageNewName
                ];

                if ($db->insert('teachers', $data)) {
                    echo '<div class="alert alert-success">Registration successful!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to register.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">File size exceeds 10MB.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Error uploading files.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Invalid file format.</div>';
    }
}
?>
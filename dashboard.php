<?php
include_once 'system/autoload.php';
include_once 'modules/Teacher.php';
checkloginSession();
$db = new Database();
$conn = $db->connect();

$teacher = new Teacher($db);
$teacher_id  =$_SESSION['teacher_id'];
$teacher_data = $teacher->getByID($teacher_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Teacher Dashboard</title>
</head>
<body>

<div class="container mt-5">
<?php include_once 'template/navbar.php'; ?>

    <h2>Teacher Dashboard</h2>
    <p>Name: <?= htmlspecialchars($teacher_data['name']) ?></p>
    <p>Email: <?= htmlspecialchars($teacher_data['email']) ?></p>
    <p>Resume: <a href="uploads/resumes/<?= htmlspecialchars($teacher_data['resume']) ?>" target="_blank">View Resume</a></p>
    <p>Image: <img src="uploads/images/<?= htmlspecialchars($teacher_data['image']) ?>" alt="Teacher Image" width="100"></p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
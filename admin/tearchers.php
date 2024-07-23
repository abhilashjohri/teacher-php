<?php 
include_once '../system/autoload.php';
include_once '../modules/Teacher.php';
checkloginSession();

?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container mt-5">
<?php include_once '../template/navbar.php'; ?>
<div class="row">
    <div class="col-6">
    <h2>Teachers Listing</h2>
</div>
<div class="col-6">
   <button class="btn btn-primary btn-sm add-btn float-end" data-id="">Add Teacher </button>
   </div>
   </div>
   <div id="message" class="mt-3"></div>
    <table id="teachersTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Resume</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table> 
</div>

<!-- Modal for Add/Edit Teacher -->
<div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="teacherModalLabel">Add/Edit Teacher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="teacherForm" enctype="multipart/form-data">
          <input type="hidden" id="teacherId" name="id">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" >
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" >
          </div>
          <div class="mb-3">
            <label for="resume" class="form-label">Resume</label>
            <input type="file" class="form-control" id="resume" name="resume" >
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image" >
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
      SITE_URL = "<?php echo SITE_URL; ?>";
$(document).ready(function() {
    var table = $('#teachersTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": SITE_URL+"ajax_action.php",
            "type": "POST",
            "data": { action: 'listTeacher',module: 'teacher' }
        },
        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "email" },
            { "data": "resume" },
            { "data": "image" },
            { "data": "status" },
            { "data": "actions" },
          
        ]
    });

    // Handle edit button click
    $('#teachersTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        // Fetch teacher data and populate form for editing
        $.post(SITE_URL+'ajax_action.php', { action: 'get_teacher', id: id,module: 'teacher'  }, function(data) {
            var teacher = JSON.parse(data);
            $('#teacherId').val(teacher.id);
            $('#name').val(teacher.name);
            $('#email').val(teacher.email);
            $('#password').val('');
            $('#teacherModal').modal('show');
        });
    });
// Handle add  button click
      $(".add-btn").click(function(){
          $('#teacherId').val('');
          $('#teacherForm')[0].reset();
          $('#teacherModal').modal('show');
        });

    // Handle delete button click
    $('#teachersTable').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this teacher?')) {
            $.post(SITE_URL+'ajax_action.php', { action: 'delete_teacher',module: 'teacher' , id: id }, function(response) {
                table.ajax.reload();
            });
        }
    });

    // Handle status button click
    $('#teachersTable').on('click', '.status-btn', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var newStatus = status == 1 ? 0 : 1;
        $.post(SITE_URL+'ajax_action.php', { action: 'update_status',module: 'teacher' , id: id, status: newStatus }, function(response) {
            table.ajax.reload();
        });
    });

    // Handle form submission for adding/editing teacher
    $('#teacherForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'saveTeacher');
        formData.append('module', 'teacher');
        $.ajax({
            url: SITE_URL+'ajax_action.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const obj = JSON.parse(response);
                class_name = 'success';
                 if(obj.status =='error'){
                    class_name ='danger';  
                 }
                msg =  '<div class="alert alert-'+class_name+'">'+obj.msg+'</div>';
                $('#message').html(msg);
                 if(obj.status =='success'){
                   $('#teacherModal').modal('hide');
                   table.ajax.reload();
                 }
            }
        });
    });
});
</script>
</body>
</html>
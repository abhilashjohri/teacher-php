<?php include_once 'system/autoload.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Teacher Login</title>
</head>
<body>
<div class="container mt-5">
    <h2>Teacher Login</h2>
    <form id="loginForm">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="<?php echo  SITE_URL;?>/register.php" class="btn btn-secondary">Register</a>
        <input type="hidden"  name="module" value="teacher">
        <input type="hidden"  name="action" value="login">

    </form>
    <div id="message" class="mt-3"></div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script>
  SITE_URL = "<?php echo SITE_URL; ?>";
 
$(function () {

  $('#loginForm').validate({
    rules: {
      email: {
        required: true,
        email: true,
      },
      password: {
        required: true,
        minlength: 5
      }
    },
    messages: {
      email: {
        required: "Please enter a email address",
        email: "Please enter a valid email address"
      },
      password: {
        required: "Please provide a password",
        minlength: "Your password must be at least 5 characters long"
      },
      terms: "Please accept our terms"
    },
     submitHandler: function (form) {
    //  alert( "Form successful submitted!" );
        event.preventDefault();
       
        var formData = $(form).serialize();
        $.ajax({
          url:SITE_URL+"ajax_action.php",
          method:"POST",
          data:formData,
          success: function(response) {
                $('#message').html(response);
                if (response.indexOf('success') >= 0) {
                    window.location.href = 'dashboard.php';
                }
            },statusCode: {
            404: function() {
              alert( "page not found" );
            
            },
            500: function() {
              alert( "Please try again later" );
          
            }
          }
        })
    }

  });
});
</script>

</body>
</html>
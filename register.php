<?php include_once 'system/autoload.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Teacher Registration</title>
</head>
<body>
<div class="container mt-5">
    <h2>Teacher Registration</h2>
    <form id="registrationForm" method="post" enctype="multipart/form-data">
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
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="mb-3">
            <label for="resume" class="form-label">Resume</label>
            <input type="file" class="form-control" id="resume" name="resume" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="<?php echo  SITE_URL;?>index.php" class="btn btn-secondary">login</a>
        <input type="hidden"  name="module" value="teacher">
        <input type="hidden"  name="action" value="register">
    </form>
    <div id="message" class="mt-3"></div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script>
      SITE_URL = "<?php echo SITE_URL; ?>";
$(function () {
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0}');

    $.validator.addMethod('extension', function(value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "pdf|doc|docx|jpg|jpeg|png";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, 'Please enter a valid file type.');

    $('#registrationForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            },
            resume: {
                required: true,
                extension: "pdf|doc|docx",
                filesize: 10485760 // 10 MB
            },
            image: {
                required: true,
                extension: "jpg|jpeg|png",
                filesize: 10485760 // 10 MB
            }
        },
        messages: {
            name: {
                required: "Please enter your name",
                minlength: "Your name must be at least 2 characters long"
            },
            email: {
                required: "Please enter your email",
                email: "Please enter a valid email address"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            },
            confirm_password: {
                required: "Please confirm your password",
                equalTo: "Password and confirm password do not match"
            },
            resume: {
                required: "Please upload your resume",
                extension: "Only PDF, DOC, and DOCX files are allowed",
                filesize: "Resume must be less than 10 MB"
            },
            image: {
                required: "Please upload your image",
                extension: "Only JPG, JPEG, and PNG files are allowed",
                filesize: "Image must be less than 10 MB"
            }
        },
        errorClass: 'error',
        highlight: function(element) {
            $(element).addClass('error-input');
            $(element).closest('.mb-3').find('label').addClass('error-label');
        },
        unhighlight: function(element) {
            $(element).removeClass('error-input');
            $(element).closest('.mb-3').find('label').removeClass('error-label');
        },
        submitHandler: function(form, event) {
            event.preventDefault();
            var formData = new FormData(form);
            formData.append('action', 'register');

            $.ajax({
                url: SITE_URL + "ajax_action.php",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#message').html(response);
                    if (response.indexOf('success') >= 0) {
                        window.location.href = 'dashboard.php';
                    }
                },
                statusCode: {
                    404: function() {
                        alert("Page not found");
                    },
                    500: function() {
                        alert("Please try again later");
                    }
                }
            });
        }
    });
});
</script>

</body>
</html>
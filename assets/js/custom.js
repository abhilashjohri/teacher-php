$(document).ready(function(){
    $('#registrationForm').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        
        // Image validation
        var image = $('#image')[0].files[0];
        var imageType = image.type;
        var imageSize = image.size;
        var validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        if ($.inArray(imageType, validImageTypes) < 0) {
            $('#message').html('<div class="alert alert-danger">Invalid image format. Only jpg, jpeg, and png are allowed.</div>');
            return;
        }

        if (imageSize > 10485760) {
            $('#message').html('<div class="alert alert-danger">Image size exceeds 10MB.</div>');
            return;
        }

        // Resume validation
        var resume = $('#resume')[0].files[0];
        var resumeType = resume.type;
        var resumeSize = resume.size;
        var validResumeTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if ($.inArray(resumeType, validResumeTypes) < 0) {
            $('#message').html('<div class="alert alert-danger">Invalid resume format. Only pdf, doc, and docx are allowed.</div>');
            return;
        }

        if (resumeSize > 10485760) {
            $('#message').html('<div class="alert alert-danger">Resume size exceeds 10MB.</div>');
            return;
        }

        $.ajax({
            url: 'ajax_action.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#message').html(response);
            }
        });
    });
});
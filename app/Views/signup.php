<div class="container-sm" style="max-width: 450px; border:1px solid #d8dee2">
    <?php $errors = \Config\Services::validation()->getErrors(); //best wasy to show the errors;
    $sess = \Config\Services::session();
    ?>
    <?= form_open_multipart('signup/register'); ?>

    <p class="text-center h3 mt-2"> Signup</p>

    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="text" name="e_mail" value="<?= set_value('e_mail') ?>" id="email" class="form-control" placeholder="johndoe@example.com" aria-describedby="helpId" required="true">
        <small id="helpId" class="text-muted">E-mail will not be shared with anyone</small>
    </div>
    <div class="form-group">
        <label for="mobile">Mobile</label>
        <input type="tel" name="mobile" value="<?= set_value('mobile') ?>" id="mobile" class="form-control" placeholder="Your mobile" aria-describedby="helpId" required="true">
        <small id="helpId" class="text-muted">Mobile will not be shared with anyone</small>
    </div>
    <div class="form-group">
        <label for="Fname">First Name</label>
        <input type="text" name="fname" value="<?= set_value('fname') ?>" id="Fname" class="form-control" placeholder="Put Your First Name" required="true">
    </div>
    <div class="form-group">
        <label for="Lname">Last Name</label>
        <input type="text" name="lname" value="<?= set_value('lname') ?>" id="Lname" class="form-control" placeholder="Put Your Last Name" required="true">
    </div>
    <div class="form-group">
        <label for="Username">Username</label>
        <input type="text" name="uname" value="<?= set_value('uname') ?>" id="Username" class="form-control" placeholder="Username" required="true">
        <small id="helpId" class="text-muted">Must be unique</small>
    </div>
    <div class="form-group">
        <label for="pass_wd">Password</label>
        <input type="password" name="passwd" value="<?= set_value('passwd') ?>" id="pass_wd" class="form-control" placeholder="Password" required="true">
    </div>
    <div class="form-group">
        <label for="re-pass">Confirm-password</label>
        <input type="password" name="confirm_pass" value="<?= set_value('confirm_pass') ?>" id="re-pass" class="form-control" placeholder="Re-type password" required="true">
    </div>
    <div class="form-group">
        <label for="image">Image</label>
        <input type="file" name="image" id="image" class="form-control" required="true">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-secondary btn-block" name="sub" id="formsub" value="SignUp">
    </div>
    <?= form_close(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!--  toastr is a Javascript library for non-blocking notifications.  -->
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "7000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
    <?php
    #display error in js notification
    if (!empty($errors)) {
        foreach ($errors as $key => $error) {
            echo '<script>toastr.error("' , esc($error) , '");</script>';
        }
    }
    #display session value in js notification
    if($sess->has('username')){
        $val = $_SESSION['username'];
        echo '<script>toastr.warning("',esc($val),'")</script>';
        $sess->destroy();
    }
    ?>
</div>
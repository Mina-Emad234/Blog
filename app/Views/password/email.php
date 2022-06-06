

<div class="container mt-5" style="width: 100%; margin: 0 auto;max-width: 600px;padding: 15px;border: 1px solid #d8dee2;background-color: white;">
    <?php $errors = \Config\Services::validation()->getErrors(); //best wasy to show the errors;
    if (!empty($errors)):
     foreach ($errors as $key => $error):
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= $error ?></div>
            <button type="button" class="close pl-0" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php
      endforeach;
      endif;
      $session=Session();
      $session->start();
     if($session->has('wrong_email')):?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= $session->get('wrong_email') ?></div>
            <button type="button" class="close pl-0" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif;
    if($session->has('try_again')):?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= $session->get('try_again') ?>></div>
            <button type="button" class="close pl-0" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?= form_open('/reset_password/sendEmail'); ?>
    <?= csrf_field(); ?>

        <label for="mail">Write your E-mail</label>
        <?php $email_data = ["id" => "mail", "class" => "form-control", "style"=>'width:500px;display:inline-block !important',"type" => "email", "name" => "email", "placeholder" => "Your E-mail","value"=>set_value('email'),"autocomplete"=>"off"]; ?>
        <?= form_input($email_data); ?>
        <input class="btn btn-danger mb-1" type="submit" name="" value="Send">


    <?= form_close(); ?>
</div>


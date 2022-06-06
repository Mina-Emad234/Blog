

<div class="container mt-5" style="width: 100%; margin: 0 auto;max-width: 600px;padding: 15px;border: 1px solid #d8dee2;background-color: white;height: 180px">
    <?php
    $session=Session();
    $session->start();
    $error = \Config\Services::validation()->getError('verify'); //best wasy to show the errors;
    if (!empty($error)):
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= $error ?></div>
            <button type="button" class="close pl-0" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php
      endif;
     if($session->has('wrong_verify')):?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= $session->get('wrong_verify') ?>></div>
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

    <?= form_open('/reset_password/sendCode'); ?>
    <?= csrf_field(); ?>
    <input type="hidden" name="rand_id" value="<?= $user_rand_id ?>">
    <label for="mail">Write code which is contains of <strong>6 digits</strong></label>
    <div class="form-group">
        <?php $verify_data = ["id" => "mail", "class" => "display-block", "style"=>'width:200px;',"type" => "text", "name" => "verify", "placeholder" => "Your Code", 'value'=>set_value('verify'),"autocomplete"=>"off"]; ?>
        <?= form_input($verify_data); ?>
    <div>
    <div class="form-group">
        <input class="btn btn-danger float-right" type="submit" value="Verify">
    </div>

    <?= form_close(); ?>
</div>


<style>
    .submit-btn,
    input[type="submit"]:hover {
        background-image: linear-gradient(-180deg, #34d058, #00aa00 90%);
        background-color: #00aa00;
        color: white;
        font-weight: 700;
    }
</style>

<div class="container mt-4" style="width: 100%; margin: 0 auto;max-width: 450px;padding: 15px;border: 1px solid #d8dee2;background-color: white;">
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
    if($session->has('wrong_data')):?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= $session->get('wrong_data') ?>></div>
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


    <?= form_open('/reset_password/resetPassword'); ?>
    <?= csrf_field(); ?>
    <input type="hidden" name="rand_id" value="<?= $user_rand_id ?>">
    <div class="form-group">
        <label for="mail">password</label>
        <?php $pass_data = ["id" => "mail", "class" => "form-control", "type" => "password", "name" => "password", 'value'=>set_value('password')]; ?>
        <?= form_password($pass_data); ?>
    </div>

    <div class="form-group">
        <label for="password">Confirm Password</label>
        <?php $confirm_pass_data = ["id" => "password", "class" => "form-control", "type" => "password", "name" => "confirm_password", 'value'=>set_value('confirm_password')]; ?>
        <?= form_password($confirm_pass_data); ?>
    </div>

    <div class="mt-4">
        <input class="btn btn-block btn-success submit-btn" type="submit" name="" value="Reset">
    </div>

    <?= form_close(); ?>
</div>
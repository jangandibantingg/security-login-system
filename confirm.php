<?php

include "ASEngine/AS.php";

if (! isset($_GET['k'])) {
    redirect('login.php');
}

$key = $_GET['k'];
$valid = app('validator')->confirmationKeyValid($key);

if ($valid) {
    app('db')->update(
        'as_users',
        array("confirmed" => "Y"),
        "`confirmation_key` = :k",
        array("k" => $key)
    );
}

?>
<!doctype html>
<html lang="en"> 
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Advanced Security - PHP MySQL Register/Login System">
        <meta name="author" content="Milos Stojanovic">

        <title><?= trans('email_confirmation'); ?> | Advanced Security</title>

        <link rel='stylesheet' href='assets/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='ASLibrary/css/style3.css' type='text/css' media='all' />
    </head>
    <body>
        <div class="container">
            <div class="modal modal-visible" id="confirm-modal" style="display: inherit;">
                <div class="modal-dialog" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><?= WEBSITE_NAME; ?></h3>
                        </div>
                        <div class="modal-body">
                            <div class="well">
                                <?php if ($valid) : ?>
                                    <h4 class="text-success text-center"><?= trans('email_confirmed') ?></h4>
                                    <h5 class="text-success text-center">
                                        <?= trans('you_can_login_now', array('link' => 'login.php')) ?>
                                    </h5>
                                <?php else : ?>
                                    <h4 class="text-error text-center"><?= trans('user_with_key_doesnt_exist') ?></h4>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
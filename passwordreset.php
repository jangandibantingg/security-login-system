<?php

include "ASEngine/AS.php";

if (! isset($_GET['k'])) {
    redirect('login.php');
}

$valid = app('validator')->prKeyValid($_GET['k']);
?>
<!doctype html>
<html lang="en"> 
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Advanced Security - PHP MySQL Register/Login System">
        <meta name="author" content="Milos Stojanovic">
        
        <title><?= trans('password_reset'); ?> | Advanced Security</title>

        <link rel='stylesheet' href='assets/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='ASLibrary/css/style3.css' type='text/css' media='all' />

        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="ASLibrary/js/js-bootstrap.php"></script>
    </head>
    <body>
        <div class="container">
            <div class="modal modal-visible" id="password-reset-modal">
                <div class="modal-dialog" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><?= WEBSITE_NAME; ?></h3>
                        </div>
                        <div class="modal-body">
                            <div class="well">
                                <?php if ($valid) : ?>
                                    <form class="form-horizontal" id="password-reset-form">
                                        <fieldset>
                                            <div id="legend">
                                                <legend class=""><?= trans('password_reset'); ?></legend>
                                            </div>

                                            <div class="control-group form-group">
                                                <label class="control-label col-lg-4"  for="login-username">
                                                    <?= trans('new_password'); ?>
                                                </label>
                                                <div class="controls col-lg-8">
                                                    <input type="password" id="password-reset-new-password"
                                                           class="input-xlarge form-control" />
                                                </div>
                                            </div>

                                            <div class="control-group form-group">
                                                <div class="controls col-lg-offset-4 col-lg-8">
                                                    <button id="btn-reset-pass" class="btn btn-success">
                                                        <?= trans('reset_password'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                <?php else : ?>
                                    <h5 class="text-error text-center"><?= trans('invalid_password_reset_key') ?></h5>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="assets/js/sha512.js"></script>
        <script type="text/javascript" src="ASLibrary/js/asengine.js"></script>
        <script type="text/javascript" src="ASLibrary/js/passwordreset.js"></script>

    </body>
</html>
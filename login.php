<?php 
include "ASEngine/AS.php";

if (app('login')->isLoggedIn()) {
    redirect('index.php');
}

$token = app('register')->socialToken();
ASSession::set('as_social_token', $token);
app('register')->botProtection();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Advanced Security - PHP MySQL Register/Login System">
        <meta name="author" content="Milos Stojanovic">
        <title>Login | Advanced Security</title>

        <link rel='stylesheet' href='assets/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='ASLibrary/css/style3.css' type='text/css' media='all' />

        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="ASLibrary/js/js-bootstrap.php"></script>
    </head>
    <body>
        <div class="container">
            <div class="flags-wrapper">
                <?php include 'templates/languages.php'; ?>
            </div>

            <div class="modal modal-visible" id="loginModal">
                <div class="modal-dialog" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><?= WEBSITE_NAME; ?></h3>
                        </div>
                        <div class="modal-body">
                            <div class="well">

                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#login" data-toggle="tab"><?= trans('login'); ?></a></li>
                                    <li><a href="#create" data-toggle="tab"><?= trans('create_account'); ?></a></li>
                                    <li><a href="#forgot" data-toggle="tab"><?= trans('forgot_password'); ?></a></li>
                                </ul>

                                <div class="tab-content">
                                    <!-- start: Login Tab -->
                                    <div class="tab-pane active in" id="login">
                                        <form class="form-horizontal">
                                            <fieldset>
                                                <div id="legend">
                                                    <legend class=""><?= trans('login'); ?></legend>
                                                </div>

                                                <!-- start: Username -->
                                                <div class="control-group form-group">
                                                    <label class="control-label col-lg-4"  for="login-username">
                                                        <?= trans('username'); ?>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                      <input type="text" id="login-username" name="username"
                                                             class="input-xlarge form-control"> <br />
                                                    </div>
                                                </div>
                                                <!-- end: Username -->

                                                <!-- start: Password -->
                                                <div class="control-group form-group">
                                                    <label class="control-label col-lg-4" for="login-password">
                                                        <?= trans('password'); ?>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="password" id="login-password"
                                                               name="password" class="input-xlarge form-control">
                                                    </div>
                                                </div>
                                                <!-- end: Password -->

                                                <div class="control-group form-group">
                                                    <div class="controls col-lg-offset-4 col-lg-8">
                                                    <button id="btn-login" class="btn btn-success">
                                                        <?= trans('login'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                                    <!-- end: Login Tab -->

                                    <!-- start: Registration Tab -->
                                    <div class="tab-pane fade" id="create">
                                        <form class="form-horizontal register-form" id="tab">
                                            <fieldset>
                                                <div id="legend">
                                                    <legend class=""><?= trans('create_account'); ?></legend>
                                                </div>

                                                <div class="control-group  form-group">
                                                    <label class="control-label col-lg-4" for='reg-email' >
                                                        <?= trans('email'); ?> <span class="required">*</span>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="text" id="reg-email" class="input-xlarge form-control">
                                                    </div>
                                                </div>

                                                <div class="control-group  form-group">
                                                    <label class="control-label col-lg-4" for="reg-username">
                                                        <?= trans('username'); ?> <span class="required">*</span>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="text" id="reg-username" class="input-xlarge form-control">
                                                    </div>
                                                </div>

                                                <div class="control-group  form-group">
                                                    <label class="control-label col-lg-4" for="reg-password">
                                                        <?= trans('password'); ?> <span class="required">*</span>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="password" id="reg-password" class="input-xlarge form-control">
                                                    </div>
                                                </div>

                                                <div class="control-group  form-group">
                                                    <label class="control-label col-lg-4" for="reg-repeat-password">
                                                        <?= trans('repeat_password'); ?> <span class="required">*</span>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="password" id="reg-repeat-password"
                                                               class="input-xlarge form-control">
                                                    </div>
                                                </div>

                                                <div class="control-group  form-group">
                                                    <label class="control-label col-lg-4" for="reg-bot-sum">
                                                        <?php echo ASSession::get("bot_first_number"); ?> +
                                                        <?php echo ASSession::get("bot_second_number"); ?>
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="text" id="reg-bot-sum" class="input-xlarge form-control">
                                                    </div>
                                                </div>

                                                <div class="control-group  form-group">
                                                    <div class="controls col-lg-offset-4 col-lg-8">
                                                        <button id="btn-register" class="btn btn-success">
                                                            <?= trans('create_account'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                           </fieldset>
                                        </form>
                                    </div>
                                    <!-- end: Registration Tab -->

                                    <!-- start: Forgot Password Tab -->
                                    <div class="tab-pane in" id="forgot">
                                        <form class="form-horizontal" id="forgot-pass-form">
                                            <fieldset>
                                                <div id="legend">
                                                    <legend class=""><?= trans('forgot_password'); ?></legend>
                                                </div>
                                                <div class="control-group form-group">
                                                    <label class="control-label col-lg-4" for="forgot-password-email">
                                                        <?= trans('your_email'); ?>
                                                    </label>
                                                    <div class="controls col-lg-8">
                                                        <input type="email" id="forgot-password-email"
                                                               class="input-xlarge form-control">
                                                    </div>
                                                </div>

                                                <div class="control-group form-group">
                                                    <div class="controls col-lg-offset-4 col-lg-8">
                                                        <button id="btn-forgot-password" class="btn btn-success">
                                                            <?= trans('reset_password'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                    <!-- end: Forgot Password Tab -->

                                    <!-- start: Social Login Buttons -->
                                    <div class="social-login">
                                        <?php if (TWITTER_ENABLED): ?>
                                            <a href="socialauth.php?p=twitter&token=<?php echo $token; ?>">
                                                <img src="assets/img/twitter.png" class="fade high-opacity"
                                                     alt="Twitter" title="<?= trans('login_with'); ?> Twitter"/>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (FACEBOOK_ENABLED): ?>
                                              <a href="socialauth.php?p=facebook&token=<?php echo $token; ?>">
                                                  <img src="assets/img/fb.png" class="fade high-opacity"
                                                       alt="Facebook" title="<?= trans('login_with'); ?> Facebook"/>
                                              </a>
                                        <?php endif; ?>

                                        <?php if (GOOGLE_ENABLED): ?>
                                              <a href="socialauth.php?p=google&token=<?php echo $token; ?>">
                                                  <img src="assets/img/gplus.png" class="fade high-opacity"
                                                       alt="Google+" title="<?= trans('login_with'); ?> GooglePlus"/>
                                              </a>
                                        <?php endif; ?>
                                    </div>
                                    <!-- end: Social Login Buttons -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script type="text/javascript" src="assets/js/sha512.js"></script>
        <script type="text/javascript" src="ASLibrary/js/asengine.js"></script>
        <script type="text/javascript" src="ASLibrary/js/register.js"></script>
        <script type="text/javascript" src="ASLibrary/js/login.js"></script>
        <script type="text/javascript" src="ASLibrary/js/passwordreset.js"></script>
    </body>
</html>
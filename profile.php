<?php 
include 'templates/header.php';

$currentUser = app('current_user');
?>

<div class="row">
    <?php
        $sidebarActive = 'profile';
        require 'templates/sidebar.php';
    ?>

    <div class="col-md-9 profile-details-wrapper content">

        <?php if (! $currentUser->is_admin) : ?>
            <div class="alert alert-warning" style="margin-top: 30px;">
                <strong><?= trans('note'); ?>! </strong>
                <?= trans('to_change_email_username'); ?>
            </div>
        <?php endif; ?>

        <form class="form-horizontal no-submit" id="form-changepassword">
            <fieldset>
                <!-- Form Name -->
                <legend><?= trans('change_password') ?></legend>

                <!-- Password input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="old_password">
                        <?= trans('old_password'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="old_password" name="old_password" type="password" class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Password input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="new_password">
                        <?= trans('new_password'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="new_password" name="new_password" type="password" class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Password input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="new_password_confirm">
                        <?= trans('confirm_new_password'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="new_password_confirm" name="new_password_confirm"
                               type="password" class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Button -->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="change_password"></label>
                    <div class="controls col-lg-8">
                        <button id="change_password" name="change_password" class="btn btn-primary">
                            <?= trans('update'); ?>
                        </button>
                    </div>
                </div>
            </fieldset>
        </form>

        <form class="form-horizontal no-submit" id="form-details">
            <fieldset>

                <!-- Form Name -->
                <legend><?= trans('your_details'); ?></legend>

                <!-- Text input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="first_name">
                        <?= trans('first_name'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="first_name" name="first_name" type="text"
                               value="<?= e($currentUser->first_name); ?>"
                               class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Text input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="last_name">
                        <?= trans('last_name'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="last_name" name="last_name" type="text"
                               value="<?= e($currentUser->last_name); ?>"
                               class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Text input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="address">
                        <?= trans('address'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="address" name="address" type="text"
                               value="<?= e($currentUser->address); ?>"
                               class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Text input-->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="phone">
                        <?= trans('phone'); ?>
                    </label>
                    <div class="controls col-lg-8">
                        <input id="phone" name="phone" type="text"
                               value="<?= e($currentUser->phone); ?>"
                               class="input-xlarge form-control">
                    </div>
                </div>

                <!-- Button -->
                <div class="control-group form-group">
                    <label class="control-label col-lg-4" for="update_details"></label>
                    <div class="controls col-lg-8">
                        <button id="update_details" name="update_details" class="btn btn-primary">
                            <?= trans('update'); ?>
                        </button>
                    </div>
                </div>

            </fieldset>
        </form>
    </div>
</div>

    <?php include 'templates/footer.php'; ?>
    
    <script src="assets/js/sha512.js" type="text/javascript" charset="utf-8"></script>
    <script src="ASLibrary/js/asengine.js" type="text/javascript" charset="utf-8"></script>
    <script src="ASLibrary/js/index.js" type="text/javascript" charset="utf-8"></script>
    <script src="ASLibrary/js/profile.js" type="text/javascript" charset="utf-8"></script>
    
  </body>
</html>

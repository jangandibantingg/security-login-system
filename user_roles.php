<?php

include 'templates/header.php';

if (! app('current_user')->is_admin) {
    redirect("index.php");
}

// Default roles have ids 1, 2 and 3, so we will exclude them
// from results we want to get from this query, since we want
// to know number of users for our custom roles only.
$roles = app('db')->select(
    "SELECT `as_user_roles`.*, COUNT(`as_users`.`user_id`) as num FROM `as_user_roles`
    LEFT JOIN `as_users` ON `as_users`.`user_role` = `as_user_roles`.`role_id` 
    WHERE `as_user_roles`.`role_id` NOT IN (1,2,3)
    GROUP BY `as_user_roles`.`role_id`"
);

?>

<div class="row">
    <?php
        $sidebarActive = 'roles';
        require 'templates/sidebar.php';
    ?>

    <div class="col-md-9">
        <div class="control-group roles-input">
            <div class="row">
                <div class="controls col-md-3 col-sm-3">
                    <input type="text" class="form-control col-lg-3"
                           id='role-name' placeholder="<?= trans('role_name'); ?>">
                </div>
                <button type="submit" class="btn btn-success" onclick="roles.addRole();">
                    <i class="fa fa-plus"></i>
                    <?= trans('add'); ?>
                </button>
            </div>

        </div>

        <table class="table table-striped roles-table">
            <thead>
                <th><?= trans('role_name'); ?></th>
                <th><?= trans('users_with_role'); ?></th>
                <th><?= trans('action'); ?></th>
            </thead>

            <?php foreach ($roles as $role) : ?>
                <tr class="role-row">
                    <td><?php echo e($role['role']); ?></td>
                    <td><?php echo e($role['num']); ?></td>
                    <td>
                        <button type="button" class="btn btn-danger"
                                onclick="roles.deleteRole(this,<?= $role['role_id'] ?>);">
                            <i class="glyphicon glyphicon-trash"></i>
                            <?= trans('delete'); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
    
    <?php include 'templates/footer.php'; ?>

    <script type="text/javascript" src="ASLibrary/js/asengine.js"></script>
    <script type="text/javascript" src="ASLibrary/js/roles.js"></script>
    <script type="text/javascript" src="ASLibrary/js/index.js"></script>
   	</body>
 </html>
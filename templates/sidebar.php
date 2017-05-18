<div class="col-md-3 bs-docs-sidebar">
    <ul class="nav nav-list bs-docs-sidenav">
        <li class="<?= $sidebarActive == 'home' ? 'active' : '' ?>">
            <a href="index.php">
                <i class="fa fa-home"></i>
                <i class="glyphicon glyphicon-chevron-right"></i>
                <?= trans('home'); ?>
            </a>
        </li>
        <li class="<?= $sidebarActive == 'profile' ? 'active' : '' ?>">
            <a href="profile.php">
                <i class="fa fa-user"></i>
                <i class="glyphicon glyphicon-chevron-right"></i>
                <?= trans('my_profile'); ?>
            </a>
        </li>
        <?php if (app('current_user')->is_admin) : ?>
            <li class="<?= $sidebarActive == 'users' ? 'active' : '' ?>">
                <a href="users.php">
                    <i class="fa fa-users"></i>
                    <i class="glyphicon glyphicon-chevron-right"></i>
                    <?= trans('users'); ?>
                </a>
            </li>
            <li class="<?= $sidebarActive == 'roles' ? 'active' : '' ?>">
                <a href="user_roles.php">
                    <i class="fa fa-user-secret"></i>
                    <i class="glyphicon glyphicon-chevron-right"></i>
                    <?= trans('user_roles'); ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
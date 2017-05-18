<?php

include 'templates/header.php';

if (! app('current_user')->is_admin) {
    redirect("index.php");
}

// Admin user have role id equal to 3,
// and we will omit admin from this result.
$users = app('db')->select(
    "SELECT `as_users`.*, `as_user_roles`.`role` as role_name 
    FROM `as_users` 
    INNER JOIN `as_user_roles` ON `as_users`.`user_role` = `as_user_roles`.`role_id`
    WHERE `as_users`.`user_role` != '3' 
    ORDER BY `as_users`.`register_date` DESC"
);

$roles = app('db')->select("SELECT * FROM `as_user_roles` WHERE `role_id` != '3'");

?>

<link rel="stylesheet" href="assets/css/dataTables.bootstrap.css"/>

<div class="row">
    <?php
        $sidebarActive = 'users';
        require 'templates/sidebar.php';
    ?>

    <div class="col-md-9 users-wrapper">
        <a class="btn btn-success" href="javascript:void(0);"
           onclick="users.showAddUserModal()" >
            <i class="fa fa-plus"></i>
            <?= trans('add_user') ?>
        </a>

        <div align="center" class="ajax-loading" id="loading-users" style="display: block;">
            <i class="fa fa-2x fa-circle-o-notch fa-spin"></i>
            <div class="text"><?= trans('loading') ?></div>
        </div>

        <table class="table table-striped users-table" id="users-list" width="100%" style="display: none;">
            <thead>
                <tr>
                    <th><?= trans('username') ?></th>
                    <th><?= trans('email') ?></th>
                    <th><?= trans('register_date') ?></th>
                    <th><?= trans('confirmed') ?></th>
                    <th><?= trans('action') ?></th>
                </tr>
            </thead>
            <?php foreach ($users as $user) : ?>
                <tr class="user-row">
                    <td><?= e($user['username']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= $user['register_date'] ?></td>
                    <td>
                        <?php echo $user['confirmed'] == "Y"
                            ? "<p class='text-success'>" . trans('yes') . "</p>"
                            : "<p class='text-error'>" . trans('no') . "</p>"
                        ?>
                    </td>
                    <td>
                      <div class="btn-group">
                          <a  class="btn <?= $user['banned'] == 'Y' ? 'btn-danger' : 'btn-primary'; ?> btn-user"
                              href="javascript:;"
                              onclick="users.roleChanger(this,<?= $user['user_id'] ?>, <?= $user['user_role'] ?>);">
                              <i class="fa fa-user-secret"></i>
                              <span class="user-role"><?= ucfirst($user['role_name']); ?></span>
                          </a>
                          <a data-toggle="dropdown" href="#"
                             class="btn <?= $user['banned'] == 'Y' ? 'btn-danger' : 'btn-primary'; ?> dropdown-toggle">
                              <span class="caret"></span>
                          </a>
                          <ul class="dropdown-menu">
                              <li>
                                  <a href="javascript:;"
                                     onclick="users.editUser(<?= $user['user_id'] ?>);">
                                        <i class="fa fa-edit"></i>
                                        <?= trans('edit') ?>
                                  </a>
                              </li>
                              <li>
                                  <a href="javascript:void(0);"
                                     onclick="users.displayInfo(<?= $user['user_id'] ?>);">
                                        <i class="fa fa-list-alt"></i>
                                        <?= trans('details') ?>
                                  </a>
                              </li>

                              <?php if ($user['banned'] == 'Y'): ?>
                                  <li>
                                      <a href="javascript:void(0);"
                                         onclick="users.unbanUser(this,<?= $user['user_id'] ?>);">
                                            <i class="fa fa-ban"></i>
                                            <span><?= trans('unban') ?></span>
                                      </a>
                                  </li>
                              <?php else: ?>
                                  <li>
                                      <a href="javascript:void(0);"
                                         onclick="users.banUser(this,<?= $user['user_id'] ?>);">
                                            <i class="fa fa-ban"></i>
                                            <span><?= trans('ban') ?></span>
                                      </a>
                                  </li>
                              <?php endif; ?>

                              <li>
                                  <a href="javascript:void(0);"
                                     onclick="users.deleteUser(this, <?= $user['user_id'] ?>);">
                                        <i class="fa fa-trash"></i>
                                        <?= trans('delete'); ?>
                                  </a>
                              </li>

                              <li class="divider"></li>

                              <li>
                                  <a href="javascript:void(0);"
                                     onclick="users.roleChanger(this, <?= $user['user_id'] ?>, <?= $user['user_role'] ?>);">
                                      <i class="i"></i> <?= trans('change_role'); ?>
                                  </a>
                              </li>
                          </ul>
                      </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
    
    <?php include 'templates/footer.php'; ?>
        
    <div class="modal fade" id="modal-user-details" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-username">
                        <?= trans('loading'); ?>
                    </h4>
                </div>
                <div class="modal-body" id="details-body">
                    <dl class="dl-horizontal">
                        <dt title="<?= trans('email') ?>"><?= trans('email') ?></dt>
                        <dd id="modal-email"></dd>
                        <dt title="<?= trans('first_name') ?>"><?= trans('first_name') ?></dt>
                        <dd id="modal-first-name"></dd>
                        <dt title="<?= trans('last_name') ?>"><?= trans('last_name') ?></dt>
                        <dd id="modal-last-name"></dd>
                        <dt title="<?= trans('address') ?>"><?= trans('address') ?></dt>
                        <dd id="modal-address"></dd>
                        <dt title="<?= trans('phone') ?>"><?= trans('phone') ?></dt>
                        <dd id="modal-phone"></dd>
                        <dt title="<?= trans('last_login') ?>"><?= trans('last_login') ?></dt>
                        <dd id="modal-last-login"></dd>
                    </dl>
                </div>

                <div align="center" id="ajax-loading">
                    <i class="fa fa-2x fa-circle-o-notch fa-spin"></i>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0);" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">
                        <?= trans('ok'); ?>
                    </a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="modal-change-role">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-username">
                        <?= trans('pick_user_role') ?>
                    </h4>
                </div>
                <div class="modal-body" id="details-body">
                    <?php if (count($roles) > 0) : ?>
                        <p><?= trans('select_role') ?>:</p>
                        <select id="select-user-role" class="form-control" style="width: 100%;">
                        <?php foreach($roles as $role) : ?>
                            <option value="<?= $role['role_id'] ?>">
                                <?= e(ucfirst($role['role'])) ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                        <?= trans('cancel'); ?>
                    </a>
                    <a href="javascript:;" class="btn btn-primary"
                       id="change-role-button" data-dismiss="modal" aria-hidden="true">
                        <?= trans('ok'); ?>
                    </a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="modal-add-edit-user" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-username">
                        <?= trans('add_user'); ?>
                    </h4>
                </div>
                <div class="modal-body" id="details-body">
                    <form class="form-horizontal" id="add-user-form">
                        <input type="hidden" id="adduser-userId" />
                        <div class="control-group form-group">
                            <label class="control-label col-lg-3" for="adduser-email">
                              <?= trans('email') ?>
                            </label>
                            <div class="controls col-lg-9">
                                <input id="adduser-email" name="adduser-email"
                                       type="text" class="input-xlarge form-control">
                            </div>
                        </div>

                        <div class="control-group form-group">
                            <label class="control-label col-lg-3" for="adduser-username">
                                <?= trans('username'); ?>
                            </label>
                            <div class="controls col-lg-9">
                                <input id="adduser-username" name="adduser-username"
                                       type="text" class="input-xlarge form-control">
                            </div>
                        </div>

                        <div class="control-group form-group">
                            <label class="control-label col-lg-3" for="adduser-password">
                                <?= trans('password'); ?>
                            </label>
                            <div class="controls col-lg-9">
                                <input id="adduser-password" name="adduser-password"
                                       type="password" class="input-xlarge form-control">
                            </div>
                        </div>

                        <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="adduser-confirm_password">
                            <?= trans('repeat_password'); ?>
                        </label>
                        <div class="controls col-lg-9">
                            <input id="adduser-confirm_password" name="adduser-confirm_password"
                                   type="password" class="input-xlarge form-control">
                        </div>
                    </div>

                    <hr>

                    <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="adduser-first_name">
                            <?= trans('first_name'); ?>
                        </label>
                        <div class="controls col-lg-9">
                            <input id="adduser-first_name" name="adduser-first_name"
                                   type="text" class="input-xlarge form-control">
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="adduser-last_name">
                            <?= trans('last_name'); ?>
                        </label>
                        <div class="controls col-lg-9">
                            <input id="adduser-last_name" name="adduser-last_name"
                                   type="text" class="input-xlarge form-control">
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="adduser-address">
                            <?= trans('address'); ?>
                        </label>
                        <div class="controls col-lg-9">
                            <input id="adduser-address" name="adduser-address"
                                   type="text" class="input-xlarge form-control">
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <label class="control-label col-lg-3" for="adduser-phone">
                            <?= trans('phone'); ?>
                        </label>
                        <div class="controls col-lg-9">
                            <input id="adduser-phone" name="adduser-phone"
                                   type="text" class="input-xlarge form-control">
                        </div>
                    </div>
                </form>
            </div>

                <div align="center" class="ajax-loading">
                    <i class="fa fa-2x fa-circle-o-notch fa-spin"></i>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                        <?= trans('cancel'); ?>
                    </a>
                    <a href="javascript:void(0);" id="btn-add-user" class="btn btn-primary">
                        <?= trans('add'); ?>
                    </a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

        <script type="text/javascript" src="assets/js/sha512.js"></script>
        <script type="text/javascript" src="assets/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/js/dataTables.bootstrap.js"></script>
        <script src="ASLibrary/js/asengine.js" type="text/javascript" charset="utf-8"></script>
        <script src="ASLibrary/js/users.js" type="text/javascript" charset="utf-8"></script>
        <script src="ASLibrary/js/index.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#users-list').dataTable({
                    "initComplete": function() {
                        $('#loading-users').remove();
                        $("#users-list, #users-list_wrapper").show();
                    }
                });
            });
        </script>
    </body>
</html>
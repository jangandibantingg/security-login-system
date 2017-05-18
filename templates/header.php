<?php

include dirname(__FILE__) . '/../ASEngine/AS.php';

if (! app('login')->isLoggedIn()) {
    redirect("login.php");
}

$currentUser = app('current_user');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?= trans('home'); ?> | Advanced Security</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Milos Stojanovic">

        <link rel='stylesheet' href='assets/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='assets/css/font-awesome.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='ASLibrary/css/style3.css' type='text/css' media='all' />
      
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="assets/js/html5shiv.js"></script>
        <![endif]-->
    </head>

    <body>
        <div id="wrap">

            <!-- start: Navbar -->
            <div class="navbar navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        <ul class="nav navbar-nav">
                            <a class="brand navbar-brand" href="./index.php"><?php echo WEBSITE_NAME;  ?></a>
                        </ul>
                        <div class="pull-right">
                            <div class="header-flags-wrapper">
                                <?php include 'templates/languages.php'; ?>
                            </div>
                            <ul class="nav pull-right">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <?= trans('welcome'); ?>, <?= e($currentUser->username);  ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="profile.php">
                                                <i class="fa fa-user"></i>
                                                <?= trans('my_profile'); ?>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="logout.php" id="logout">
                                                <i class="fa fa-sign-out"></i>
                                                <?= trans('logout'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- stop: Navbar -->
        
            <div class="container">
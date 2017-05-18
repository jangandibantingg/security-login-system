<?php

include_once 'AS.php';

$action = $_POST['action'];

switch ($action) {
    case 'checkLogin':
        app('login')->userLogin($_POST['username'], $_POST['password']);
        break;

    case "registerUser":
        app('register')->register($_POST['user']);
        break;
        
    case "resetPassword":
        app('register')->resetPassword($_POST['newPass'], $_POST['key']);
        break;
        
    case "forgotPassword":
        $result = app('register')->forgotPassword($_POST['email']);
        if ($result !== true) {
            echo $result;
        }
        break;
        
    case "postComment":
        echo app('comment')->insertComment(ASSession::get("user_id"), $_POST['comment']);
        break;
        
    case "updatePassword":
        app('user')->updatePassword(
            ASSession::get("user_id"),
            $_POST['oldpass'],
            $_POST['newpass']
        );
        break;
        
    case "updateDetails":
        app('user')->updateDetails(ASSession::get("user_id"), $_POST['details']);
        break;
        
    case "changeRole":
        onlyAdmin();

        $result = app('user')->changeRole($_POST['userId'], $_POST['role']);
        echo ucfirst($result);
        break;
        
    case "deleteUser":
        onlyAdmin();

        $userId = (int) $_POST['userId'];
        $users = app('user');

        if (! $users->isAdmin($userId)) {
            $users->deleteUser($userId);
        }
        break;
    
    case "getUserDetails":
        onlyAdmin();

        respond(
            app('user')->getAll($_POST['userId'])
        );
        break;

    case "addRole":
        onlyAdmin();

        respond(
            app('role')->add($_POST['role'])
        );
        break;

    case "deleteRole":
        onlyAdmin();

        app('role')->delete($_POST['roleId']);
        break;


    case "addUser":
        onlyAdmin();

        respond(
            app('user')->add($_POST)
        );
        break;

    case "updateUser":
        onlyAdmin();

        app('user')->updateUser($_POST['userId'], $_POST);
        break;

    case "banUser":
        onlyAdmin();

        app('user')->updateInfo($_POST['userId'], array('banned' => 'Y'));
        break;

    case "unbanUser":
        onlyAdmin();

        app('user')->updateInfo($_POST['userId'], array('banned' => 'N'));
        break;

    case "getUser":
        onlyAdmin();

        respond(
            app('user')->getAll($_POST['userId'])
        );
        break;
    
    default:
        break;
}

function onlyAdmin()
{
    if (! (app('login')->isLoggedIn() && app('current_user')->is_admin)) {
        exit();
    }
}

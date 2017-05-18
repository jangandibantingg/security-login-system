<?php

define('DEBUG', true);

if (DEBUG) {
    ini_set("display_errors", "1");
    error_reporting(E_ALL);
} else {
    ini_set("display_errors", "0");
    error_reporting(0);
}

require dirname(__FILE__) . "/../ASEngine/ASPasswordHasher.php";
require dirname(__FILE__) . "/../ASEngine/ASResponse.php";
require dirname(__FILE__) . "/../ASEngine/ASDatabase.php";
require dirname(__FILE__) . "/../ASEngine/ASHelperFunctions.php";
require dirname(__FILE__) . "/../ASEngine/ASEmail.php";
require dirname(__FILE__) . "/../ASEngine/ASValidator.php";
require dirname(__FILE__) . "/../ASEngine/ASLogin.php";
require dirname(__FILE__) . "/Installer.php";

$action = $_REQUEST['action'];

$requirements = array(
    'PHP Version (>= 5.3.0)' => version_compare(phpversion(), '5.3.0', '>='),
    'PDO Extension' => extension_loaded('PDO') && class_exists('PDO'),
    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
    'PHP Curl' => function_exists('curl_version'),
    'ASEngine Folder (writable)' => is_writable(dirname(__FILE__) . '/../ASEngine')
);

switch ($action) {
    case 'requirements':
        respond($requirements);
        break;

    case 'database':
        try {
            $db = new ASDatabase(
                'mysql',
                $_POST['host'],
                $_POST['name'],
                $_POST['username'],
                $_POST['password']
            );

            respond(['success' => true]);
        } catch (Exception $e) {
            respond(['message' => $e->getMessage()], 400);
        }
        break;

    case 'install':
        try {
            $db = new ASDatabase(
                'mysql',
                $_POST['db']['host'],
                $_POST['db']['name'],
                $_POST['db']['username'],
                $_POST['db']['password']
            );

            $stubsPath = dirname(__FILE__) . "/stubs";
            $asEnginePath = dirname(__FILE__) . "/../ASEngine";

            $domain = rtrim($_POST['domain'], '/');
            $domain = strpos($domain, 'http://') === 0 || strpos($domain, 'https://') === 0
                ? $domain
                : 'http://' . $domain;

            $scriptUrl = $domain . dirname(dirname($_SERVER['PHP_SELF']));
            $scriptUrl = rtrim($scriptUrl, '/') . "/";

            $noreplyEmail = str_replace(array('http://', 'https://'), array('', ''), $domain);
            $noreplyEmail = rtrim($noreplyEmail, "/");
            $noreplyEmail = "noreply@{$noreplyEmail}";

            $installer = new Installer($db, new ASPasswordHasher, $stubsPath, $asEnginePath);
            $installer->install(array(
                'website_name' => $_POST['name'],
                'website_domain' => $domain,
                'script_url' => $scriptUrl,
                'noreply_email' => $noreplyEmail,
                'db_host' => $_POST['db']['host'],
                'db_user' => $_POST['db']['username'],
                'db_pass' => $_POST['db']['password'],
                'db_name' => $_POST['db']['name'],
                'password_encryption' => function_exists('crypt') ? 'bcrypt' : 'sha512',
                'password_salt' => str_random(22),
            ));

            respond(['success' => true]);
        } catch (Exception $e) {
            respond(['message' => $e->getMessage()], 400);
        }
        break;

    default:
        respond(['error' => 'Action not allowed.'], 400);
}

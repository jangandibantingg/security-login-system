<?php

/**
 * Redirect to provided url
 * @param $url
 */
function redirect($url)
{
    $isExternal = stripos($url, "http://") !== false || stripos($url, "https://") !== false;

    if (! $isExternal) {
        $url = rtrim(SCRIPT_URL, '/') . '/' . ltrim($url, '/');
    }

    if (! headers_sent()) {
        header('Location: '.$url, true, 302);
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}

/**
 * Get page where user should be redirected, based on user's role.
 * If there is no specific page set for provided role, redirect to default page.
 *
 * @return string Page where user should be redirected.
 */
function get_redirect_page()
{
    $container = ASContainer::getInstance();

    if (app('login')->isLoggedIn()) {
        $role = app('user')->getRole(ASSession::get("user_id"));
    } else {
        $role = 'default';
    }

    $redirect = unserialize(SUCCESS_LOGIN_REDIRECT);

    if (! isset($redirect['default'])) {
        $redirect['default'] = 'index.php';
    }

    return isset($redirect[$role]) ? $redirect[$role] : $redirect['default'];
}


/**
 * Escape HTML entities in a string.
 *
 * @param  string  $value
 * @return string  Escaped string
 */
function e($value)
{
    return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Generates random string.
 *
 * @param int $length
 * @return string
 */
function str_random($length = 16)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}

/**
 * Get translation for specific term represented by $key param.
 *
 * @param $key
 * @param array $bindings
 * @return mixed|string
 */
function trans($key, $bindings = array())
{
    return ASLang::get($key, $bindings);
}

/**
 * Send an HTTP response.
 *
 * @param array $data
 * @param $statusCode
 */
function respond(array $data, $statusCode = 200)
{
    $response = new ASResponse();

    $response->send($data, $statusCode);
}

/**
 * Get container instance or resolve some class/service
 * out of the container.
 * @param null $service
 * @return mixed
 */
function app($service = null)
{
    $c = ASContainer::getInstance();

    if (is_null($service)) {
        return $c;
    }

    return $c[$service];
}

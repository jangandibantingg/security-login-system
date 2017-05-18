<?php


class ASCsrf
{
    /**
     * CSRF Token name.
     */
    const TOKEN_NAME = "_as_csrf_token";

    /**
     * (Re-)Generate a token and write it to session
     *
     * @return void
     */
    public static function generateToken()
    {
        // generate as random of a token as possible for lower PHP versions
        ASSession::set(
            self::TOKEN_NAME,
            sha1(uniqid(sha1(PASSWORD_SALT), true) . time() . str_random(20))
        );
    }
    /**
     * Get the token.  If it's not defined, this will go ahead and generate one.
     *
     * @return string
     */
    public static function getToken()
    {
        if (! ASSession::get(self::TOKEN_NAME)) {
            static::generateToken(self::TOKEN_NAME);
        }

        return ASSession::get(self::TOKEN_NAME);
    }
    /**
     * Get CSRF token name.
     *
     * @return string
     */
    public static function getTokenName()
    {
        return self::TOKEN_NAME;
    }
    /**
     * Validate the token.  If there's not one yet, it will set one and return false.
     *
     * @param array $requestData - your whole POST/GET array - will index in with the token name to get the token.
     * @return bool
     */
    public static function validate($requestData = array())
    {
        if (! self::isValidRequest()) {
            return false;
        } elseif (! ASSession::get(self::TOKEN_NAME)) {
            static::generateToken();
            return false;
        } elseif (empty($requestData[self::TOKEN_NAME])) {
            return false;
        } else {
            return static::compare($requestData[self::TOKEN_NAME], static::getToken());
        }
    }

    private static function isValidRequest()
    {
        if (self::isAjaxRequest() && self::isValidReferer()) {
            return true;
        }

        return false;
    }

    private static function isAjaxRequest()
    {
        return ! empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private static function isValidReferer()
    {
        $url = parse_url(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

        if (! isset($url['host'])) {
            return false;
        }

        $allowedHosts = array(
            $url['host'],
            "www.".$url['host'],
            str_replace("www.", '', $url['host'])
        );

        if (! in_array($_SERVER['SERVER_NAME'], $allowedHosts)) {
            return false;
        }

        return true;
    }

    /**
     * Constant-time string comparison.  This comparison function is timing-attack safe.
     *
     * @param string $hasha
     * @param string $hashb
     * @return bool
     */
    private static function compare($hasha = "", $hashb = "")
    {
        // we want hashes_are_not_equal to be false by the end of this if the strings are identical
        // if the strings are NOT equal length this will return true, else false
        $areNotEqual = strlen($hasha) ^ strlen($hashb);

        // compare the shortest of the two strings (the above line will still
        // kick back a failure if the lengths weren't equal.  this just keeps us
        // from over-flowing our strings when comparing
        $length = min(strlen($hasha), strlen($hashb));
        $hasha = substr($hasha, 0, $length);
        $hashb = substr($hashb, 0, $length);

        // iterate through the hashes comparing them character by character
        // if a character does not match, then return true, so the hashes are not equal
        for ($i = 0; $i < strlen($hasha); $i++) {
            $areNotEqual += !(ord($hasha[$i]) === ord($hashb[$i]));
        }
        // if not hashes are not equal, then hashes are equal
        return !$areNotEqual;
    }
}

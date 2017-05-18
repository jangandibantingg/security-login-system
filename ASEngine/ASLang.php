<?php

/**
 * Advanced Security - PHP Register/Login System
 *
 * @author Milos Stojanovic
 * @link   http://mstojanovic.net/as
 */

/**
 * Class ASLang
 */
class ASLang
{
    /**
     * Get whole language file with all terms.
     * @param bool $jsonEncode Determine should data be encoded in json or not
     * @return mixed|string Array or JSON that contains whole language file of current language.
     */
    public static function all($jsonEncode = true)
    {
        // determine language
        $language = self::getLanguage();

        // get translation for determined language
        $trans = self::getTrans($language);
        
        if ($jsonEncode) {
            return json_encode($trans);
        }

        return $trans;
    }

    /**
     * Get translation for specific term represented by $key param
     * @param $key string Term
     * @param array $bindings If term value contains some variables (:name, :username or similar)
     * this array should contain values that those variables should be replaced with.
     * @return mixed|string
     */
    public static function get($key, $bindings = array())
    {
        // determine language
        $language = self::getLanguage();

        // get translation array
        $trans = self::getTrans($language);

        // if term (key) doesn't exist, return empty string
        if (! isset($trans[$key])) {
            return '';
        }

        // term exist, get the value
        $value = $trans[$key];

        // replace variables with provided bindings
        if (! empty($bindings)) {
            foreach ($bindings as $key => $val) {
                $value = str_replace('{'.$key.'}', $val, $value);
            }
        }

        return $value;
    }

    /**
     * Set script language
     * @param $language string Language that should be set
     * @return bool
     */
    public static function setLanguage($language)
    {
        // check if language is valid
        if (! self::isValidLanguage($language)) {
            return false;
        }

        //set language cookie to 1 year
        setcookie('as_lang', $language, time() + 60 * 60 * 24 * 365, '/');

        // update session
        ASSession::set('as_lang', $language);

        unset($_GET['lang']);
        $queryString = http_build_query($_GET);

        $redirect = $_SERVER['PHP_SELF'];

        if ($queryString) {
            $redirect .= "?{$queryString}";
        }

        redirect($redirect);
    }

    /**
     * Get current language
     * @return mixed String abbreviation of current language
     */
    public static function getLanguage()
    {
        // Get language from cookie if there is valid lang cookie
        if (isset($_COOKIE['as_lang']) && self::isValidLanguage($_COOKIE['as_lang'])) {
            return $_COOKIE['as_lang'];
        }

        return ASSession::get('as_lang', DEFAULT_LANGUAGE);
    }

    /**
     * Get translation array for provided language
     * @param $language string Language to get translation array for
     * @return mixed Translation array.
     * @throws Exception
     */
    private static function getTrans($language)
    {
        $file = self::getFile($language);

        if (self::isValidLanguage($language)) {
            $language = include $file;
            return $language;
        }

        throw new Exception('Language file does not exist!');
    }

    /**
     * Get language file path from lang directory
     * @param $language string
     * @return string
     */
    private static function getFile($language)
    {
        return dirname(dirname(__FILE__)) . '/Lang/' . $language . '.php';
    }

    /**
     * Check if language is valid (if file for given language exist in Lang folder)
     * @param $lang string Language to validate
     * @return bool TRUE if language file exist, FALSE otherwise
     */
    private static function isValidLanguage($lang)
    {
        $file = self::getFile($lang);

        return file_exists($file);
    }
}

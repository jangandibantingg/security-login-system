<?php

/**
 * Advanced Security - PHP Register/Login System
 *
 * @author Milos Stojanovic
 * @link   http://mstojanovic.net/as
 */

/**
 * Class ASContainer
 */
class ASContainer
{
    /**
     * @var
     */
    protected static $instance;

    /**
     * @param \Pimple\Container $container
     */
    public static function setContainer(\Pimple\Container $container)
    {
        self::$instance = $container;
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}

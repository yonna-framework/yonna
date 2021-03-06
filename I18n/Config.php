<?php

namespace Yonna\I18n;

use Yonna\Foundation\System;

class Config
{

    private static $database = null;
    private static $auto = null;

    /**
     * @param $key
     * @param null $default
     * @return array|bool|false|string|null
     */
    public static function env($key, $default = null)
    {
        return System::env($key, $default);
    }

    /**
     * @return null
     */
    public static function getDatabase()
    {
        return self::$database;
    }

    /**
     * @param null $database
     */
    public static function setDatabase($database): void
    {
        self::$database = $database;
    }

    /**
     * @return null
     */
    public static function getAuto()
    {
        return self::$auto;
    }

    /**
     * @param null $auto
     */
    public static function setAuto($auto): void
    {
        self::$auto = $auto;
    }

}
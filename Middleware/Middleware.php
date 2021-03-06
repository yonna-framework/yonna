<?php

namespace Yonna\Middleware;

use Yonna\Core;
use Yonna\IO\Request;
use Yonna\Scope\Scope;
use Yonna\Throwable\Exception;

/**
 * class Middleware
 * @package Core\Core\scope
 */
abstract class Middleware implements Interfaces\Middleware
{

    protected static string $type = MiddlewareType::MIDDLEWARE;

    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return static::$type;
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
    }

    /**
     * @param string $call scope class name
     * @param string $action function name
     * @return mixed
     * @throws Exception\ThrowException
     */
    public function scope(string $call, string $action)
    {
        $Scope = Core::get($call, $this->request());
        if (!$Scope instanceof Scope) {
            Exception::throw("Class {$call} is not instanceof Log");
        }
        return $Scope->$action();
    }

}
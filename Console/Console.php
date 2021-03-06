<?php

namespace Yonna\Console;

use Exception;

/**
 * 命令台
 * Class Console
 * @package Core\Core\Console
 */
class Console
{

    /**
     * @param array $opts
     * @param $field
     * @return bool
     * @throws Exception
     */
    protected function checkParams(array $opts, array $field)
    {
        if (empty($field)) {
            return true;
        }
        $err = null;
        foreach ($field as $f) {
            if (!isset($opts[$f])) {
                $err = "Params: {$f} is not Exist";
                break;
            }
        }
        if ($err !== null) {
            throw new Exception($err);
        }
        return true;
    }

}


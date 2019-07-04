<?php

namespace VMSConnect\Integrations;

use VMSConnect\Integrations\iVMS;

abstract class BaseVMS implements iVMS
{
    /**
     * @var string 
     */
    protected $host;

    /**
     * Returns fully qualified class name
     * @return string
     */
    public static function fqcn()
    {
        return __CLASS__;
    }
}

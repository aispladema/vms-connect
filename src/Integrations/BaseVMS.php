<?php

namespace VMSConnect\Integrations;

use VMSConnect\Integrations\iVMS;

abstract class BaseVMS implements iVMS
{
    /**
     * @var string 
     */
    protected $host;
}

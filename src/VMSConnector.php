<?php


namespace VMSConnect;


class VMSConnector
{

    public $type;
    public $name;
    public $host;
    public $port;
    public $user;
    public $password;
    public $options;

    /**
     * VMSConnector constructor.
     * @param array $parameters Provide name, host, port, user, password and options
     */
    function __construct($parameters = array()) {
        foreach($parameters as $key => $value) {
            $this->$key = $value;
        }
    }
}
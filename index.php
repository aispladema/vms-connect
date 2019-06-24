<?php

require_once "vendor/autoload.php";

header('Content-Type: application/json');

try{
    $class = new \VMSConnect\Integrations\Digifort("192.168.7.211", [
        'auth' => [
            'user' => 'smartcam',
            'password' => 'scMunicipio2017!'
        ]
    ]);

    echo json_encode($class->getCameras());

}
catch(Exception $e)
{
    print_r($e);
}
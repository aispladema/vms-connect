<?php

require_once "vendor/autoload.php";

$class = new \VMSConnect\Integrations\Digifort("192.168.7.211", [
  'auth' => [
    'user' => 'smartcam',
    'password' => 'scMunicipio2017!'
  ]
]);

echo $class->getCameras();

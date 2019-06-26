<?php

require_once "vendor/autoload.php";

date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json');


try{
    $class = new \VMSConnect\Integrations\Digifort("192.168.7.211", [
        'auth' => [
            'user' => 'smartcam',
            'password' => 'scMunicipio2017!'
        ]
    ]);

    $files = $class->export("Entrada Pladema", \Carbon\Carbon::now()->subSeconds(300), \Carbon\Carbon::now()->subSeconds(60), __DIR__ . "/storage");

    echo json_encode($files);

}
catch(Exception $e)
{
    print_r($e);
}
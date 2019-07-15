<?php

require_once "vendor/autoload.php";

use VMSConnect\Integrations\Digifort;
use VMSConnect\VMSConnector;

date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json');

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="localhost"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Ingrese el usuario de contraseña del conector';
    exit;
} 

try{

    $connector = new VMSConnector([
       "type" => Digifort::fqcn(),
       "host" => "192.168.7.211",
       "port" => "8601",
       'user' => $_SERVER['PHP_AUTH_USER'],
       'password' => $_SERVER['PHP_AUTH_PW']
    ]);

    $class = \VMSConnect\ConnectorFactory::getConnector($connector);

    //$timelines = $class->getTimelines("Entrada Pladema", \Carbon\Carbon::parse('2019-06-27 05:00:00'), \Carbon\Carbon::parse('2019-06-27 15:00:00'));
    //echo json_encode($timelines);
    
    //$cameras = $class->getCameras();
    //echo json_encode($cameras);

    //$cameras = $class->getStatus();
    //echo json_encode($cameras);

    //$camera = $class->getCameraById("Entrada Pladema");
    //echo json_encode($camera);

    //$camera = $class->getStatusByCameraId("Entrada Pladema");
    //echo json_encode($camera);


    //Snapshot
    //header('Content-Type: text/html');
    //$image = $class->getSnapshot("Fija 099");
    //$image = base64_encode($image);
    //echo "<img src='data:image/jpeg;base64, $image' />";

    //Export
    //$files = $class->export("Entrada Pladema", \Carbon\Carbon::now()->subSeconds(300), \Carbon\Carbon::now()->subSeconds(60), __DIR__ . "/storage");
    //echo json_encode($files);

}
catch(Exception $e)
{
    print_r($e);
}
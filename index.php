<?php

require_once "vendor/autoload.php";

use VMSConnect\Integrations\Digifort;
use VMSConnect\VMSConnector;

date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json');


try{

    $connector = new VMSConnector([
       "type" => Digifort::fqcn(),
       "host" => "192.168.7.211",
       "port" => "8601",
       'user' => 'smartcam',
       'password' => 'scMunicipio2017!'
    ]);

    $class = \VMSConnect\ConnectorFactory::getConnector($connector);

    $timelines = $class->getTimeline("Entrada Pladema", \Carbon\Carbon::parse('2019-06-27 05:00:00'), \Carbon\Carbon::parse('2019-06-27 14:00:00'));
    echo json_encode($timelines);
    
    $cameras = $class->getCameras();
    echo json_encode($cameras);
    //$files = $class->export("Entrada Pladema", \Carbon\Carbon::now()->subSeconds(300), \Carbon\Carbon::now()->subSeconds(60), __DIR__ . "/storage");
    //echo json_encode($files);

}
catch(Exception $e)
{
    print_r($e);
}
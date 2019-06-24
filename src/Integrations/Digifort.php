<?php

namespace VMSConnect\Integrations;

use Carbon\Carbon;
use Nathanmac\Utilities\Parser\Parser;

class Digifort extends BaseVMS 
{

  public function __construct($host, $options = [])
  {
    $this->host = $host;
    $this->client = new \GuzzleHttp\Client;
    if(!empty($options) && !empty($options['auth']))
    {
      $this->client = new \GuzzleHttp\Client([
          'auth' => [$options['auth']['user'], $options['auth']['password']],
      ]); 
    }
  }

  public function getVersion(){
    $url = sprintf("http://%s:8601/Interface/GetApiVersion", $this->host);
    $response = $this->client->request('GET', $url);
    $body = $response->getBody()->getContents();
    
    $xml = new \SimpleXMLElement($body);
    
    if ( 0 != count($xml) )
    {
      $name = $xml->Data->ApiVersion->Name;
      $version = $xml->Data->ApiVersion->Version;
      return sprintf("%s %s", $name, $version);
    } 

    return "Error";
  }

  public function getCameras(){
    $url = sprintf("http://%s:8601/Interface/Cameras/GetCameras", $this->host);
    $response = $this->client->request('GET', $url);
    $body = $response->getBody()->getContents();
    
    $xml = new \SimpleXMLElement($body);
    
    if ( !empty($xml) && !empty($xml->Data)  )
    {
      $cameras = [];
      foreach ($xml->Data->Cameras as $camera) {
        if( isset($camera->Camera) ) {
          array_push($cameras, [
            "name" => (string) $camera->Camera->Name,
            "description" => (string) $camera->Camera->Description,
            "latitude" => (float) $camera->Camera->Latitude,
            "longitude" => (float) $camera->Camera->Longitude,
            "active" => (string) $camera->Camera->Active == 'TRUE'
          ]);
        }
      }
      return json_encode($cameras);
    } 

    return [];
  }

  public function getSnapshot($camera){
    throw new Exception('Not implemented');
  }

  public function getJPEGStream($camera){
    throw new Exception('Not implemented');
  }

  public function export($camera, Carbon $start, Carbon $end){
    throw new Exception('Not implemented');
  }

  public function downloadExport($exportLocation){
    throw new Exception('Not implemented');
  }

}

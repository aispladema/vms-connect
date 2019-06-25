<?php

namespace VMSConnect\Integrations;

use Carbon\Carbon;
use Nathanmac\Utilities\Parser\Parser;
use GuzzleHttp\Client;

class Digifort extends BaseVMS
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    private $options;

    /**
     * Digifort constructor.
     * @param string $host
     * @param array $options
     */
    public function __construct(string $host, $options = [])
    {
        $this->host = $host;
        $this->options = $options;
        $this->client = new \GuzzleHttp\Client;
        if (!empty($options) && !empty($options['auth'])) {
            $this->client = new \GuzzleHttp\Client([
                'auth' => [$options['auth']['user'], $options['auth']['password']],
            ]);
        }
    }

    public function getVersion(): string
    {
        $url = sprintf("http://%s:8601/Interface/GetApiVersion", $this->host);
        $response = $this->client->request('GET', $url);
        $body = $response->getBody()->getContents();

        $xml = new \SimpleXMLElement($body);

        if (0 != count($xml)) {
            $name = $xml->Data->ApiVersion->Name;
            $version = $xml->Data->ApiVersion->Version;
            return sprintf("%s %s", $name, $version);
        }

        return "Error";
    }

    public function getCameras(): array
    {
        $url = sprintf("http://%s:8601/Interface/Cameras/GetCameras", $this->host);
        $response = $this->client->request('GET', $url);
        $body = $response->getBody()->getContents();

        $xml = new \SimpleXMLElement($body);

        if (!empty($xml) && !empty($xml->Data)) {
            $cameras = [];
            foreach ($xml->Data->Cameras as $camera) {
                if (isset($camera->Camera)) {
                    array_push($cameras, [
                        "name" => (string)$camera->Camera->Name,
                        "description" => (string)$camera->Camera->Description,
                        "latitude" => (float)$camera->Camera->Latitude,
                        "longitude" => (float)$camera->Camera->Longitude,
                        "active" => (string)$camera->Camera->Active == 'TRUE'
                    ]);
                }
            }
            return $cameras;
        }

        return [];
    }

    public function getSnapshot(string $camera): array
    {
        throw new Exception('Not implemented');
    }

    public function getJPEGStream(string $camera): string
    {
        throw new Exception('Not implemented');
    }

    public function export(string $camera, Carbon $start, Carbon $end, string $outputPath): array
    {
        $files = $this->startExport($camera, $start,  $end);

        $response = [];
        foreach ($files as $file) {
            $path = $this->downloadExport($file['sessionId'], $file['filename'], $outputPath);
            $response[] = $path;
        }

        if ($files)
            $this->closeExport(current($files)['sessionId']);

        return $response;
    }

    private function startExport(string $camera, Carbon $start, Carbon $end): array {
        $url = sprintf("http://%s:8601/Interface/Cameras/Playback/StartExport", $this->host);

        $query =  [
            'Camera' => $camera,
            'StartDate' => $start->format("Y.m.d"),
            'StartTime' => $start->format("H.i.s.0000"),
            'EndDate' => $end->format("Y.m.d"),
            'EndTime' => $end->format("H.i.s.0000")
            ];

        $response = $this->client->request('GET', $url, ['query' => $query]);
        $body = $response->getBody()->getContents();

        $files = [];
        $xml = new \SimpleXMLElement($body);
        if (!empty($xml) && !empty($xml->Data) && $xml->Message == "OK") {
            $sessionId = (string)$xml->Data->Export->ID;
            foreach ($xml->Data->Export->Files as $file) {
                if (isset($file->File)) {
                    array_push($files, [
                        "sessionId" => $sessionId,
                        "filename" => (string)$file->File->Filename,
                        "size" => (string)$file->File->Size
                    ]);
                }
            }
        }

        return $files;
    }

    private function downloadExport(string $sessionId, string $filename, string $outputPath): string {
        //Example request
        // http://192.168.7.211:8601/Interface/Cameras/Playback/GetExportedFile
        //  ?SessionID=93F275EB5F3FD45596410BE754075197
        //  &Filename=93F275EB5F3FD45596410BE754075197_1.mp4
        //  &ResponseFormat=XML
        $url = sprintf("http://%s:8601/Interface/Cameras/Playback/GetExportedFile", $this->host);

        $query =  [
            'SessionID' => $sessionId,
            'Filename' => $filename,
        ];

        //$response = $this->client->request('GET', $url, ['query' => $query])

        // Download file
        $tmpFile = tempnam($outputPath, 'vms-connect-download');
        $tmpFile .= ".mp4";
        $handle = fopen($tmpFile, 'w');


        $options = array(
            'base_uri' => '',
            'verify' => false,
            'sink' => handle,
            'curl.options' => array(
//                'CURLOPT_RETURNTRANSFER' => true,
//                'CURLOPT_FILE' => $handle
            ),
            'query' => $query,
        );

       // $client = new Client($options);

        $res = $this->client->get($url, $options);
        echo $res->getStatusCode() . "\n";
        echo $res->getHeaderLine('content-type') . "\n";
        //fclose($handle);

        return $tmpFile;
    }

    private function closeExport(string $sessionId) {

    }
}

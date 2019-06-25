<?php

namespace VMSConnect\Integrations;

use Carbon\Carbon;
use Nathanmac\Utilities\Parser\Parser;

class Digifort extends BaseVMS
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Digifort constructor.
     * @param string $host
     * @param array $options
     */
    public function __construct(string $host, $options = [])
    {
        $this->host = $host;
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
        return $outputPath . "/" . $filename;
    }

    private function closeExport(string $sessionId) {

    }
}

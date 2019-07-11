<?php

//declare(strict_types=1);

namespace VMSConnect\Integrations;

use Carbon\Carbon;
use http\Encoding\Stream;

interface iVMS
{
    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @return array
     */
    public function getCameras(): array;

    /**
     * @param string $camera Camera ID
     * @return byte[]
     */
    public function getSnapshot(string$camera): array;

    /**
     * @param string $camera Camera ID
     * @return string Url
     */
    public function getJPEGStream(string $camera): string;

    /**
     * @param string $camera Camera ID
     * @param Carbon $start Recorded sequence start DateTime
     * @param Carbon $end Recorded sequence end DateTime
     * @param string $outputPath
     * @return array
     */
    public function export(string $camera, Carbon $start, Carbon $end, string $outputPath): array;

     /**
     * @param string $camera Camera ID
     * @param Carbon $start Recorded sequence start DateTime
     * @param Carbon $end Recorded sequence end DateTime
     * @return array
     */
    public function getTimelines(string $camera, Carbon $start, Carbon $end): array;
}
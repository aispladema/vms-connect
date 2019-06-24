<?php

//declare(strict_types=1);

namespace VMSConnect\Integrations;

use Carbon\Carbon;

interface iVMS
{
  public function getVersion();
  public function getCameras();
  public function getSnapshot($camera);
  public function getJPEGStream($camera);
  public function export($camera, Carbon $start, Carbon $end);
  public function downloadExport($exportLocation);

}
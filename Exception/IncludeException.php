<?php

namespace Productive\Exception;

class IncludeException extends \Exception {
    public $realLine;
    public $realFile;
}

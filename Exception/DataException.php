<?php

namespace Productive\Exception;

class DataException extends \Exception {
    public $realLine;
    public $realFile;
}

<?php

namespace App\Exception;

class CsvNotOpenException extends \Exception
{
    public function __construct(string $filePath, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Le fichier situé %s ne peux être lu', $filePath);
        parent::__construct($message, $code, $previous);
    }
}
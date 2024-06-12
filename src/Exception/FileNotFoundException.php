<?php

namespace App\Exception;

class FileNotFoundException extends \Exception
{
    public function __construct(string $filePath, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Le chemin %s n\'existe pas', $filePath);
        parent::__construct($message, $code, $previous);
    }
}
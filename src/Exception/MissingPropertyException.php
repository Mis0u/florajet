<?php

namespace App\Exception;

use Throwable;

class MissingPropertyException extends \Exception
{
    public function __construct(string $missingProperty, int $index,int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('La propriété %s à l\'index %d est manquante.', $missingProperty, $index);
        parent::__construct($message, $code, $previous);
    }
}
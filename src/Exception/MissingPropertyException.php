<?php

namespace App\Exception;

use Throwable;

class MissingPropertyException extends \Exception
{
    public function __construct(string $missingProperty, int|string $index,int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('La propriété %s à l\'index %s est manquante.', $missingProperty, $index);
        parent::__construct($message, $code, $previous);
    }
}
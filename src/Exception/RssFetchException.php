<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class RssFetchException extends \HttpException
{
    public function __construct(int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $message = 'Erreur lors de la récupération du flux RSS';
        parent::__construct($message, $code);
    }
}
<?php

namespace App\Service\ArticleAggregator\Api;

use App\Exception\MissingPropertyException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchApi
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @param string[] $requiredProperties
     * @return string[]
^    */
    public function fetchApi(string $url, array $requiredProperties,string $articles): array
    {

       $response =  $this->httpClient->request('GET', $url);
       $statusCode = $response->getStatusCode();

       $this->handleStatusCode($statusCode);
       $content = $response->toArray();
       $this->checkProperties($content[$articles], $requiredProperties);

       return $content;
    }

    private function handleStatusCode(int $statusCode): void
    {
        if ($statusCode !== Response::HTTP_OK) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @param string[] $data
     * @param string[] $requiredProperties
     * @throws MissingPropertyException
     */
    private function checkProperties(array $data, array $requiredProperties): void
    {
        foreach ($data as $index => $item) {
            foreach ($requiredProperties as $requiredProperty) {
                if (!isset($item[$requiredProperty])) {
                    throw new MissingPropertyException($requiredProperty, $index);
                }
            }
        }
    }
}
<?php

namespace App\Service\ArticleAggregator\Rss;

use App\Exception\MissingPropertyException;
use App\Exception\RssFetchException;
use SimpleXMLElement;

class FetchRss
{
    public function fetchRss($url, array $properties)
    {
        $xml = simplexml_load_file($url);
        $this->handleError($xml);
        $this->checkProperties($xml, $properties);

        return $xml;
    }

    private function handleError(SimpleXMLElement|false $xml): void
    {
        if (false === $xml) {
            throw new RssFetchException();
        };
    }

    public function checkProperties(SimpleXMLElement $data, array $requiredProperties): void
    {
        foreach ($data as $index => $item) {
            foreach ($requiredProperties as $requiredProperty) {
                if (!isset($item->$requiredProperty)) {
                    throw new MissingPropertyException($requiredProperty, $index);
                }
            }
        }
    }
}
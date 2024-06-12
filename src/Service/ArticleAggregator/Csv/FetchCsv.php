<?php

namespace App\Service\ArticleAggregator\Csv;

use App\Exception\CsvNotOpenException;
use App\Exception\FileNotFoundException;
use App\Exception\MissingPropertyException;

class FetchCsv
{
    /**
     * @return resource|false
     */
    public function fetchCsv(string $filePath, array $properties)
    {
        $this->handleError($filePath);
        $this->checkProperties($filePath, $properties);

        return fopen($filePath, 'r');
    }

    private function handleError(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        if (!fopen($filePath, 'r')) {
            throw new CsvNotOpenException($filePath);
        }
    }

    private function checkProperties(string $filePath, array $properties)
    {
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle, 1000, ';');
        fclose($handle);
        if ($header !== $properties) {
            throw new \Exception("Le fichier CSV ne contient pas les colonnes attendues: " . implode(', ', $properties));
        }
    }
}
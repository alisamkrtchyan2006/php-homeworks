<?php

declare(strict_types=1);

namespace App\Services;

use App\Iface\ValidationException;

class CsvManagement
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        if (!file_exists($this->filePath)) {
            if (file_put_contents($this->filePath, '') === false) {
                throw new ValidationException("Failed to create file: {$this->filePath}");
            }
        }
    }

    public function readCsv(): array
    {
        if (!file_exists($this->filePath)) {
            throw new ValidationException("File not found: {$this->filePath}");
        }

        if (!is_readable($this->filePath)) {
            throw new ValidationException("The file is not available for reading: {$this->filePath}");
        }

        $handle = fopen($this->filePath, 'r');
        if ($handle === false) {
            throw new ValidationException("Failed to open file for reading: {$this->filePath}");
        }

        $rows = [];

        while (($data = fgetcsv($handle, 0, ",", '"', "\\")) !== false) {
            $rows[] = $data;
        }

        fclose($handle);
        return $rows;
    }

    public function writeCsv(array $data): void
    {
        $handle = fopen($this->filePath, 'w');

        if ($handle === false) {
            throw new ValidationException("Failed to open file for writing: {$this->filePath}");
        }

        foreach ($data as $row) {
            $rowToWrite = array_map(function($item) {
                if (is_object($item)) {
                    if (method_exists($item, 'getName')) {
                        return $item->getName();
                    }
                    return (string)$item;
                }
                return $item;
            }, $row);

            if (fputcsv($handle, $rowToWrite, ",", '"', "\\") === false) {
                fclose($handle);
                throw new ValidationException("Error writing to CSV: {$this->filePath}");
            }
        }

        fclose($handle);
    }


    public function appendCsv(array $row): void
    {
        $handle = fopen($this->filePath, 'a');

        if ($handle === false) {
            throw new ValidationException("Failed to open file for appending: {$this->filePath}");
        }

        if (fputcsv($handle, $row, ",", '"', "\\") === false) {
            fclose($handle);
            throw new ValidationException("Error appending to CSV: {$this->filePath}");
        }

        fclose($handle);
    }

}

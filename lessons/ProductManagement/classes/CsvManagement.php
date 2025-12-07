<?php

declare(strict_types=1);

class CsvManagement
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, '');
        }
    }

    public function readCsv(): array
    {
        if (!is_readable($this->filePath)) {
            return [];
        }

        $rows = [];
        if (($handle = fopen($this->filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ",", '"', "\\")) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    public function writeCsv(array $data): void
    {
        if (($handle = fopen($this->filePath, 'w')) !== false) {
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

                fputcsv($handle, $rowToWrite, ",", '"', "\\");
            }
            fclose($handle);
        }
    }

}

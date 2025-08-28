<?php

namespace Xvlvv\Util;

class CsvSqlConverter implements SqlConverterInterface
{

    public function toSQL(\SplFileObject $file, string $tableName, string $dbName): void
    {
        $resultFile = new \SplFileObject("$dbName-$tableName.sql", 'w');
        $file->rewind();
        $tableColumns = trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $file->fgets()));
        $tableColumnsArray = str_getcsv($tableColumns);
        $tableColumnsCount = count($tableColumnsArray);
        $tableColumns = implode(', ', $tableColumnsArray);
        $resultFile->fwrite(
            <<<SQL
        USE {$dbName};
        INSERT INTO {$tableName} ({$tableColumns})
        VALUES
        
        SQL
        );
        $isFirstLine = true;
        while (!$file->eof()) {
            $row = $file->fgetcsv();
            if (empty($row) || count($row) !== $tableColumnsCount) {
                continue;
            }
            $row = array_map(fn($value) => "'" . addslashes($value) . "'", $row);
            $str = implode(', ', $row);
            if (!$isFirstLine) {
                $resultFile->fwrite(",\n");
            }
            $resultFile->fwrite("($str)");
            $isFirstLine = false;
        }
        $resultFile->fwrite(';');
    }
}
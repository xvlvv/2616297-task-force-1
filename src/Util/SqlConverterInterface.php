<?php

namespace Xvlvv\Util;

interface SqlConverterInterface
{
    public function toSQL(\SplFileObject $file, string $tableName, string $dbName): void;
}
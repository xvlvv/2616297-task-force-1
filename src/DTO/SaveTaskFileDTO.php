<?php

namespace Xvlvv\DTO;

/**
 * DTO для передачи данных о загруженном файле задания.
 * Содержит информацию, необходимую для сохранения файла в базе данных.
 */
readonly final class SaveTaskFileDTO
{
    /**
     * @param string $originalName Оригинальное имя файла, как оно было у пользователя.
     * @param string $filePath Путь к сохраненному файлу на сервере.
     */
    public function __construct(
        public string $originalName,
        public string $filePath,
    ) {
    }
}
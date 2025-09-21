<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

/**
 * Сущность Категория
 */
readonly final class Category
{
    /**
     * @param int $id ID категории
     * @param string $name Название категории
     */
    public function __construct(
        private int $id,
        private string $name,
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
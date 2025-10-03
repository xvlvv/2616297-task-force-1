<?php

declare(strict_types=1);

namespace Xvlvv\Entity;

/**
 * Доменная сущность Город.
 */
final class City
{
    /**
     * @param int $id ID города
     * @param string $name Название города
     * @param string|null $boundingBox Ограничивающий прямоугольник для гео-поиска
     */
    public function __construct(
        private int $id,
        private string $name,
        private ?string $boundingBox,
    ) {
    }

    /**
     * Возвращает ID города.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает название города.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает строку с координатами ограничивающего прямоугольника.
     * @return string|null
     */
    public function getBoundingBox(): ?string
    {
        return $this->boundingBox;
    }

    /**
     * Обновляет координаты ограничивающего прямоугольника.
     * @param string|null $boundingBox Новые координаты.
     * @return void
     */
    public function updateBoundingBox(?string $boundingBox): void
    {
        if (null !== $boundingBox) {
            $this->boundingBox = $boundingBox;
        }
    }
}
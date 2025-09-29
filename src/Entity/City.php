<?php

declare(strict_types = 1);

namespace Xvlvv\Entity;

/**
 * Сущность Город
 */
final class City
{
    /**
     * @param int $id ID города
     * @param string $name Название города
     */
    public function __construct(
        private int $id,
        private string $name,
        private ?string $boundingBox,
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

    public function getBoundingBox(): ?string
    {
        return $this->boundingBox;
    }

    public function updateBoundingBox(?string $boundingBox): void
    {
        if (null !== $boundingBox) {
            $this->boundingBox = $boundingBox;
        }
    }
}
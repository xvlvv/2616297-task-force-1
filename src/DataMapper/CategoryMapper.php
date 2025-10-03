<?php

declare(strict_types=1);

namespace Xvlvv\DataMapper;

use app\models\Category as ARCategory;
use Xvlvv\Entity\Category as DomainCategory;

/**
 * Маппер для преобразования данных между ActiveRecord моделью Category и доменной сущностью Category.
 */
final class CategoryMapper
{
    /**
     * Преобразует ActiveRecord модель в доменную сущность.
     *
     * @param ARCategory $arCategory ActiveRecord модель категории.
     * @return DomainCategory Доменная сущность категории.
     */
    public function toDomainEntity(ARCategory $arCategory): DomainCategory
    {
        return new DomainCategory(
            $arCategory->id,
            $arCategory->name
        );
    }
}
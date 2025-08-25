<?php

namespace Xvlvv\DTO;

use Xvlvv\Domain\User\Role\Customer;
use Xvlvv\Domain\ValueObject\Coordinates;
use Xvlvv\Entity\Category;
use Xvlvv\Entity\City;
use Xvlvv\Entity\User;

readonly final class SaveTaskDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public Category $category,
        public User $customer,
        public ?\DateTimeImmutable $endDate = null,
        public ?Coordinates $coordinates = null,
        public ?int $budget = null,
        public ?City $city = null,
        public array $fileIds = []
    ) {
    }
}
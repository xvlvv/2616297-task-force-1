<?php

namespace Xvlvv\Repository;

use Xvlvv\Entity\User;

interface UserRepositoryInterface
{
    public function getById(int $id): ?User;
    public function getByIdOrFail(int $id): User;
    public function getByEmailOrFail(string $email): User;
    public function update(User $user): void;
    public function save(User $user): User;
    public function isUserExistsByEmail(string $email): bool;
}
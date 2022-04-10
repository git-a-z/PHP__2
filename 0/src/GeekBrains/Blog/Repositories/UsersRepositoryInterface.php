<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\User;

interface UsersRepositoryInterface extends RepositoryInterface
{
    public function save(User $user): void;
    public function get(int $id): User;
    public function getByUsername(string $username): User;
}
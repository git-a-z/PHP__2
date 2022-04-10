<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\User;
use GeekBrains\Blog\Exceptions\UserNotFoundException;

class MemoryUserRepository implements MemoryUserRepositoryInterface
{
    protected array $users;

    public function save(User $user)
    {
        $this->users[$user->getId()] = $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        if(!in_array($id, array_keys($this->users)))
        {
            throw new UserNotFoundException('User not found');
        }

        return $this->users[$id];
    }
}
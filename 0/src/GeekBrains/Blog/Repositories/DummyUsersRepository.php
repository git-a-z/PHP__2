<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\User;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use JetBrains\PhpStorm\Pure;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        // Ничего не делаем
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
    }
    
    #[Pure] public function getByUsername(string $username): User
    {
        // Нас интересует реализация только этого метода
        // Для нашего теста не важно, что это будет за пользователь,
        // поэтому возвращаем совершенно произвольного
        return new User(0, "user123", "first", "last");
    }
}
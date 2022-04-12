<?php

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;

class JsonBodyIdIdentification implements AuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {}

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            // Получаем ID пользователя из JSON-тела запроса;
            // ожидаем, что корректный ID находится в поле user_id
            $userId = $request->jsonBodyField('user_id');
        } catch (HttpException $e) {
            // Если невозможно получить ID из запроса -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
            // Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userId);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким ID не найден -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
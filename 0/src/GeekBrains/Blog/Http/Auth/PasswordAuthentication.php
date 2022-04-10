<?php

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {}

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль
        // соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        // Пользователь аутентифицирован
        return $user;
    }
}
<?php

namespace GeekBrains\Blog\Http\Auth;

use DateTimeImmutable;
use GeekBrains\Blog\Exceptions\AuthTokenNotFoundException;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\AuthTokensRepositoryInterface;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    // Bearer — на предъявителя
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository,
        // Репозиторий пользователей
        private UsersRepositoryInterface $usersRepository,
    ) {}

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        // Отрезаем префикс Bearer
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        // Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        // Получаем ID пользователя из токена
        $userId = $authToken->userId();

        // Ищем и возвращаем пользователя
        return $this->usersRepository->get($userId);
    }
}
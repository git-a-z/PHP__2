<?php

namespace GeekBrains\Blog\Http\Actions\Auth;

use DateTimeImmutable;
use Exception;
use GeekBrains\Blog\AuthToken;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Auth\AuthException;
use GeekBrains\Blog\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\AuthTokensRepositoryInterface;

class LogIn implements ActionInterface
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {}

    /**
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->getId(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );

        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);

        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}
<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Psr\Log\LoggerInterface;

class CreateUserCommand implements CommandInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        // Добавили зависимость от логгера
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");
            // Вместо выбрасывания исключения просто выходим из функции
            return;
        }

        // Создаём объект пользователя
        // Функция createFrom сама создаст UUID
        // и захеширует пароль
        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            $arguments->get('first_name'),
            $arguments->get('last_name')
        );
        $this->usersRepository->save($user);

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: $username");
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }
}
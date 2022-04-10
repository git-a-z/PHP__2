<?php

namespace GeekBrains\Blog;

use GeekBrains\Traits\Id;
use JetBrains\PhpStorm\Pure;

class User
{
    use Id;

    public function __construct(
        private string $username,
        // Переименовали поле password
        private string $hashedPassword,
        private string $firstName,
        private string $lastName
    ) {}

    // Переименовали функцию
    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    // Функция для вычисления хеша
    private static function hash(string $password, string $username): string
    {
        return hash('sha256', $username . $password);
    }

    // Функция для проверки предъявленного пароля
    #[Pure] public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->username);
    }

    // Функция для создания нового пользователя
    #[Pure] public static function createFrom(
        string $username,
        string $password,
        string $firstName,
        string $lastName
    ): self
    {
        return new self(
            $username,
            self::hash($password, $username),
            $firstName,
            $lastName
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function __toString()
    {
        return sprintf('[%d] %s %s (%s)', 
            $this->id, $this->firstName, $this->lastName, $this->username);
    }
}
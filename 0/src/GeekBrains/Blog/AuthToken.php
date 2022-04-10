<?php

namespace GeekBrains\Blog;

use DateTimeImmutable;

class AuthToken
{
    public function __construct(
        // Строка токена
        private string $token,
        // ID пользователя
        private int $userId,
        // Срок годности
        private DateTimeImmutable $expiresOn
    ) {}

    public function token(): string
    {
        return $this->token;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }
}
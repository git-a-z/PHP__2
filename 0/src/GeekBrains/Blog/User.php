<?php

namespace GeekBrains\Blog;

class User
{
    public function __construct(
        private int $id,
        private string $username,
        private string $firstName,
        private string $lastName
    ) {}

    public function getId(): int
    {
        return $this->id;
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
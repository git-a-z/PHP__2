<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Enums\Argument;
use GeekBrains\Blog\Exceptions\MatchException;

class RepositoryFactory
{
    /**
     * @throws MatchException
     */
    public function create(string $type): RepositoryInterface
    {
        return match ($type)
        {
            Argument::USER => new SqliteUsersRepository(),
            Argument::POST => new SqlitePostsRepository(),
            Argument::COMMENT => new SqliteCommentsRepository(),
            Argument::LIKE => new SqliteLikesRepository(),
            default => throw new MatchException(
                sprintf(
                    "Первый аргумент должен содержать одно из перечисленных значений: '%s'.",
                    implode("', '", Argument::$ARGUMENTS)
                )
            )
        };
    }  
}
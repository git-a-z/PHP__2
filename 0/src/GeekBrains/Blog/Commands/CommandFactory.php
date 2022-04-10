<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Enums\Argument;
use GeekBrains\Blog\Exceptions\MatchException;
use GeekBrains\Blog\Repositories\RepositoryInterface;
use Psr\Log\LoggerInterface;

class CommandFactory
{
    /**
     * @throws MatchException
     */
    public function create(
        string $type,
        RepositoryInterface $repository,
        LoggerInterface $logger
    ): CommandInterface
    {
        return match ($type)
        {
            Argument::USER => new CreateUserCommand($repository, $logger),
            Argument::POST => new CreatePostCommand($repository, $logger),
            Argument::COMMENT => new CreateCommentCommand($repository, $logger),
            Argument::LIKE => new CreateLikeCommand($repository, $logger),
            default => throw new MatchException(
                sprintf(
                    "Первый аргумент должен содержать одно из перечисленных значений: '%s'.",
                    implode("', '", Argument::$ARGUMENTS)
                )
            )
        };
    }  
}
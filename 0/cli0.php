<?php

require_once 'vendor/autoload.php';

use GeekBrains\Person\Name;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Enums\Argument;
use GeekBrains\Blog\Exceptions\MatchException;

try {
    echo getResult($argv[1], Faker\Factory::create());
} catch (Throwable $th) {
    echo "{$th->getMessage()}\n";
}

/**
 * @throws MatchException
 */
function getResult($type, $faker): Comment|Name|Post
{
    return match ($type)
    {
        Argument::USER => getName($faker),
        Argument::POST => getPost($faker),
        Argument::COMMENT => getComment($faker),
        default => throw new MatchException(
            sprintf(
                "Первый аргумент должен содержать одно из перечисленных значений: '%s'.",
                implode("', '", Argument::$ARGUMENTS)
            )
        )
    };
} 

function getName($faker): Name
{
    return new Name($faker->name(5), $faker->lastname(10));
}

function getString($faker, $len) {
    return $faker->text($len);
}

function getPost($faker): Post
{
    return new Post(
        $faker->randomNumber(),
        $faker->randomNumber(),
        getString($faker, 10),
        getString($faker, 50)
    );
}

function getComment($faker): Comment
{
    return new Comment(
        $faker->randomNumber(),
        $faker->randomNumber(),
        $faker->randomNumber(),
        getString($faker, 30)
    );
}
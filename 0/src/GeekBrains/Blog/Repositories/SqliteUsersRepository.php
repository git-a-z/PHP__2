<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\User;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use PDO;
use PDOStatement;

class SqliteUsersRepository extends SqliteRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (username, first_name, last_name)
            VALUES (:username, :first_name, :last_name)'
        );

        $statement->execute([
            ':username' => $user->getUsername(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName()
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE id = :id'
        );

        $statement->execute([
            ':id' => $id,
        ]);

        return $this->getUser($statement, $id);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new UserNotFoundException(
                "Cannot find user: $username"
            );
        }

        return new User(
            $result['id'],
            $result['username'], 
            $result['first_name'], 
            $result['last_name'],
        );
    }
}
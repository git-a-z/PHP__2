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
            'INSERT INTO users (username, password, first_name, last_name)
            VALUES (:username, :password, :first_name, :last_name)'
        );

        $statement->execute([
            ':username' => $user->getUsername(),
            ':password' => $user->getHashedPassword(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
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
        $data = $statement->fetch(PDO::FETCH_OBJ);

        if (!$data) {
            throw new UserNotFoundException("Cannot find user: $username");
        }

        $user = new User(
            $data->username,
            $data->password,
            $data->first_name,
            $data->last_name,
        );

        $user->setId($data->id);

        return $user;
    }
}
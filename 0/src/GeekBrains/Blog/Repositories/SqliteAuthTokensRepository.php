<?php

namespace GeekBrains\Blog\Repositories;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GeekBrains\Blog\AuthToken;
use GeekBrains\Blog\Exceptions\AuthTokenNotFoundException;
use GeekBrains\Blog\Exceptions\AuthTokensRepositoryException;
use PDO;
use PDOException;

class SqliteAuthTokensRepository extends SqliteRepository implements AuthTokensRepositoryInterface
{
    /**
     * @throws AuthTokensRepositoryException
     */
    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,
                user_id,
                expires_on
            ) VALUES (
                :token,
                :user_id,
                :expires_on
            )
            ON CONFLICT (token) DO UPDATE SET
                expires_on = :expires_on
            SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => $authToken->token(),
                ':user_id' => (string)$authToken->userId(),
                ':expires_on' => $authToken->expiresOn()
                    ->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }

    /**
     * @throws AuthTokensRepositoryException
     */
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }

        try {
            return new AuthToken(
                $result['token'],
                $result['user_id'],
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }
}
<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Exceptions\LikeException;
use GeekBrains\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\Blog\Like;
use PDO;

class SqliteLikesRepository extends SqliteRepository implements LikesRepositoryInterface
{
    /**
     * @throws LikeException
     */
    public function save(Like $like): void
    {
        $postId = $like->getPostId();
        $userId = $like->getUserId();

        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_id = :post_id AND user_id = :user_id'
        );

        $statement->execute([
            ':post_id' => $postId,
            ':user_id' => $userId
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result !== false) {
            throw new LikeException(
                "The user: $userId has already liked the post: $postId"
            );
        }

        $statement = $this->connection->prepare(
            'INSERT INTO likes (post_id, user_id)
            VALUES (:post_id, :user_id)'
        );

        $statement->execute([
            ':post_id' => $postId,
            ':user_id' => $userId
        ]);
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByPostId(int $id): Like
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_id = :id'
        );

        $statement->execute([
            ':id' => $id,
        ]);

        $likes = [];

        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $likes[] = new Like(
                $row['id'],
                $row['post_id'],
                $row['user_id'],
            );
        }

        if (!count($likes)) {
            throw new LikeNotFoundException(
                "Cannot find any likes of post: $id"
            );
        }

        return $likes;
    }
}
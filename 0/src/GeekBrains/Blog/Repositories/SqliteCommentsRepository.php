<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\CommentNotFoundException;
use PDO;
use PDOException;

class SqliteCommentsRepository extends SqliteRepository implements CommentsRepositoryInterface
{
    public function save(Comment $comment): void
    {
        try {
            $this->connection->beginTransaction();

            $statement = $this->connection->prepare(
                'INSERT INTO comments (post_id, user_id, text)
                VALUES (:post_id, :user_id, :text)'
            );

            $statement->execute([
                ':post_id' => $comment->getPostId(),
                ':user_id' => $comment->getUserId(),
                ':text' => $comment->getText()
            ]);

            $id = $this->connection->lastInsertId();
            $comment->setId($id);

            $this->connection->commit();
        }
        catch(PDOException $e ) {
            $this->connection->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        }
    }

    /**
     * @throws CommentNotFoundException
     */
    public function get(int $id): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE id = :id'
        );

        $statement->execute([
            ':id' => $id,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new CommentNotFoundException("Cannot find comment: $id");
        }

        $comment = new Comment(
            $result['post_id'],
            $result['user_id'],
            $result['text'],
        );

        $comment->setId($result['id']);

        return $comment;
    }
}
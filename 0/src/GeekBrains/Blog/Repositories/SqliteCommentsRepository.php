<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\CommentNotFoundException;
use PDO;

class SqliteCommentsRepository extends SqliteRepository implements CommentsRepositoryInterface
{
    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (post_id, user_id, text)
            VALUES (:post_id, :user_id, :text)'
        );

        $statement->execute([
            ':post_id' => $comment->getPostId(),
            ':user_id' => $comment->getUserId(),
            ':text' => $comment->getText()
        ]);
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
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
            'INSERT INTO comments (post_id, author_id, text)
            VALUES (:post_id, :author_id, :text)'
        );

        $statement->execute([
            ':post_id' => $comment->getPostId(),
            ':author_id' => $comment->getAuthorId(),
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
            throw new CommentNotFoundException(
                "Cannot find comment: $id"
            );
        }

        return new Comment(
            $result['id'],
            $result['post_id'], 
            $result['author_id'], 
            $result['text'],
        );
    }
}
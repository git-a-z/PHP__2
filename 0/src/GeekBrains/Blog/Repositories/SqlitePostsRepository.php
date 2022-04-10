<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Post;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use PDO;

class SqlitePostsRepository extends SqliteRepository implements PostsRepositoryInterface
{
    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (author_id, title, text)
            VALUES (:author_id, :title, :text)'
        );

        $statement->execute([
            ':author_id' => $post->getAuthorId(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()
        ]);
    }

    /**
     * @throws PostNotFoundException
     */
    public function get(int $id): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE id = :id'
        );

        $statement->execute([
            ':id' => $id,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new PostNotFoundException(
                "Cannot find post: $id"
            );
        }

        return new Post(
            $result['id'],
            $result['author_id'], 
            $result['title'], 
            $result['text'],
        );
    }

    /**
     * @throws PostNotFoundException
     */
    public function delete(int $id): void
    {
        if ($this->get($id)) {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE id = :id'
            );

            $statement->execute([
                ':id' => $id
            ]);
        }
    }
}
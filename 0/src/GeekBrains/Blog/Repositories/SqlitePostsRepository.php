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
            'INSERT INTO posts (user_id, title, text)
            VALUES (:user_id, :title, :text)'
        );

        $statement->execute([
            ':user_id' => $post->getUserId(),
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
            throw new PostNotFoundException("Cannot find post: $id");
        }

        $post = new Post(
            $result['user_id'],
            $result['title'], 
            $result['text'],
        );

        $post->setId($result['id']);

        return $post;
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
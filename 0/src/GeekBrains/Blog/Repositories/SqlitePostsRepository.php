<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Post;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use PDO;
use PDOException;

class SqlitePostsRepository extends SqliteRepository implements PostsRepositoryInterface
{
    public function save(Post $post): void
    {
        try {
            $this->connection->beginTransaction();

            $statement = $this->connection->prepare(
                'INSERT INTO posts (user_id, title, text)
                VALUES (:user_id, :title, :text)'
            );

            $statement->execute([
                ':user_id' => $post->getUserId(),
                ':title' => $post->getTitle(),
                ':text' => $post->getText()
            ]);

            $id = $this->connection->lastInsertId();
            $post->setId($id);

            $this->connection->commit();
        }
        catch(PDOException $e ) {
            $this->connection->rollback();
            print "Error!: " . $e->getMessage() . "</br>";
        }
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
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE id = ?'
            );
            $statement->execute([(string)$id]);
        } catch (PDOException $e) {
            throw new PostNotFoundException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }
}
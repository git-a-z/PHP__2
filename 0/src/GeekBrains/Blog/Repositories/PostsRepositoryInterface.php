<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Post;

interface PostsRepositoryInterface extends RepositoryInterface
{
    public function save(Post $post): void;
    public function get(int $id): Post;
    public function delete(int $id): void;
}
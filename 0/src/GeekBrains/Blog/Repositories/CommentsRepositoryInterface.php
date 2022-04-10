<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Comment;

interface CommentsRepositoryInterface extends RepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(int $id): Comment;
}
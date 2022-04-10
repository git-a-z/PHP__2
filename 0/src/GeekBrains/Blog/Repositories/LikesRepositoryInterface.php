<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\Blog\Like;

interface LikesRepositoryInterface extends RepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostId(int $id): Like;
}
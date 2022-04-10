<?php

namespace GeekBrains\Blog;

class Like
{
    public function __construct(
        private int $id,
        private int $post_id,
        private int $user_id
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->post_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function __toString()
    {
        return sprintf('[%d]. Пользователь [%d] поставил лайк статье [%d]',
            $this->id, $this->user_id, $this->post_id);
    }
}
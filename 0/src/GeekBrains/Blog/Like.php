<?php

namespace GeekBrains\Blog;

use GeekBrains\Traits\Id;

class Like
{
    use Id;

    public function __construct(
        private int $post_id,
        private int $user_id
    ) {}

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
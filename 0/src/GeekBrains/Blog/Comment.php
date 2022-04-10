<?php

namespace GeekBrains\Blog;

use GeekBrains\Traits\Id;

class Comment
{
    use Id;

    public function __construct(
        private int $post_id,
        private int $user_id,
        private string $text
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

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString()
    {
        return sprintf('[%d]. Пользователь [%d] комментирует статью [%d] — %s', 
            $this->id, $this->user_id, $this->post_id, $this->text);
    }
}
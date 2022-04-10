<?php

namespace GeekBrains\Blog;

use GeekBrains\Traits\Id;

class Post
{
    use Id;

    public function __construct(
        private int $user_id,
        private string $title,
        private string $text
    ) {}

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString()
    {
        return sprintf('[%d]. Автор [%d] пишет в статье "%s" — %s', 
            $this->id, $this->user_id, $this->title, $this->text);
    }
}
<?php

namespace GeekBrains\Blog;

class Comment
{
    public function __construct(
        private int $id,
        private int $post_id,
        private int $author_id,
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

    public function getAuthorId(): int
    {
        return $this->author_id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString()
    {
        return sprintf('[%d]. Пользователь [%d] комментирует статью [%d] — %s', 
            $this->id, $this->author_id, $this->post_id, $this->text);
    }
}
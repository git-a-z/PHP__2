<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Post;
use Psr\Log\LoggerInterface;

class CreatePostCommand implements CommandInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create post command started");

        $text = $arguments->get('text');

        $this->postsRepository->save(new Post(
            $arguments->get('user_id'),
            $arguments->get('title'),
            $text
        ));

        // Логируем информацию о новом посте
        $this->logger->info("Post created: $text");
    }
}
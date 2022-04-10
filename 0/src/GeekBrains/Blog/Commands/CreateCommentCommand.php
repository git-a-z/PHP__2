<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Comment;
use Psr\Log\LoggerInterface;

class CreateCommentCommand implements CommandInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create comment command started");

        $text = $arguments->get('text');

        $this->commentsRepository->save(new Comment(
            0,
            $arguments->get('post_id'), 
            $arguments->get('author_id'),
            $text
        ));

        // Логируем информацию о новом комментарии
        $this->logger->info("Comment created: $text");
    }
}
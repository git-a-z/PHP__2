<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\LikesRepositoryInterface;
use GeekBrains\Blog\Like;
use Psr\Log\LoggerInterface;

class CreateLikeCommand implements CommandInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create like command started");

        $postId = $arguments->get('post_id');
        $userId = $arguments->get('user_id');

        $this->likesRepository->save(new Like(
            0,
            $postId,
            $userId
        ));

        // Логируем информацию о новом лайке
        $this->logger->info("User: $userId liked post: $postId");
    }
}
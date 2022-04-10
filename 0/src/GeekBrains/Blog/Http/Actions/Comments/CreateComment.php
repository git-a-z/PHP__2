<?php

namespace GeekBrains\Blog\Http\Actions\Comments;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Auth\AuthenticationInterface;
use GeekBrains\Blog\Http\Auth\AuthException;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    // Внедряем репозитории комментариев, статей и пользователей
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        // Вместо контракта репозитория пользователей
        // внедряем контракт идентификации
        private AuthenticationInterface $authentication,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
            $userId = $user->getId();
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postId = $request->jsonBodyField('post_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти статью в репозитории
        try {
            $this->postsRepository->get($postId);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $text = $request->jsonBodyField('text');
            // Пытаемся создать объект статьи из данных запроса
            $comment = new Comment(
                $postId,
                $userId,
                $text,
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новый комментарий в репозитории
        $this->commentsRepository->save($comment);

        // Логируем создание новой комментария
        $this->logger->info("Comment created: $text");

        // Возвращаем успешный ответ, содержащий id нового комментария
        return new SuccessfulResponse([
            'post_id' => $postId,
            'user_id' => $userId,
            'text' => $text,
        ]);
    }
}
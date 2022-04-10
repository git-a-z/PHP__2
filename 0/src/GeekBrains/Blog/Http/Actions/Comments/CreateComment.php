<?php

namespace GeekBrains\Blog\Http\Actions\Comments;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    // Внедряем репозитории комментариев, статей и пользователей
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
    ) {}

    public function handle(Request $request): Response
    {
        // Пытаемся создать ID автора статьи И ID статьи из данных запроса
        try {
            $authorId = $request->jsonBodyField('author_id');
            $postId = $request->jsonBodyField('post_id');
            $text = $request->jsonBodyField('text');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти автора в репозитории
        try {
            $this->usersRepository->get($authorId);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти статью в репозитории
        try {
            $this->postsRepository->get($postId);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся создать объект статьи из данных запроса
            $comment = new Comment(
                0,
                $postId,
                $authorId,
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
            'id' => (string)$comment->getId(),
        ]);
    }
}
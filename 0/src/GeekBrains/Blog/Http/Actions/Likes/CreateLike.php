<?php

namespace GeekBrains\Blog\Http\Actions\Likes;

use GeekBrains\Blog\Exceptions\LikeException;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Auth\AuthenticationInterface;
use GeekBrains\Blog\Http\Auth\AuthException;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Like;
use GeekBrains\Blog\Repositories\LikesRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateLike implements ActionInterface
{
    // Внедряем репозитории комментариев, статей и пользователей
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
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

        try {
            $this->postsRepository->get($postId);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся создать объект лайка из данных запроса
            $like = new Like(
                $postId,
                $userId,
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Попытка сохранить новый лайк в репозитории
            $this->likesRepository->save($like);
        } catch (LikeException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Логируем создание нового лайка
        $this->logger->info("User: $userId liked post: $postId");

        // Возвращаем успешный ответ, содержащий id нового лайка
        return new SuccessfulResponse([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);
    }
}
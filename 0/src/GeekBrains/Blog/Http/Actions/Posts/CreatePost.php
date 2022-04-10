<?php

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
    ) {}

    public function handle(Request $request): Response
    {
        // Пытаемся создать ID пользователя из данных запроса
        try {
            $authorId = $request->jsonBodyField('author_id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пользователя в репозитории
        try {
            $this->usersRepository->get($authorId);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся создать объект статьи
            // из данных запроса
            $post = new Post(
                0,
                $authorId,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        // Логируем создание новой статьи
        $this->logger->info("Post created by: $authorId");

        // Возвращаем успешный ответ,
        // содержащий id новой статьи
        return new SuccessfulResponse([
            'id' => (string)$post->getId(),
        ]);
    }
}
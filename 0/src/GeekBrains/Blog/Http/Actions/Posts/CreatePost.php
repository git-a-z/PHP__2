<?php

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Auth\AuthException;
use GeekBrains\Blog\Http\Auth\TokenAuthenticationInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        // Аутентификация по токену
        private TokenAuthenticationInterface $authentication,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
    ) {}

    public function handle(Request $request): Response
    {
        // Обрабатываем ошибки аутентификации
        // и возвращаем неудачный ответ
        // с сообщением об ошибке
        try {
            $user = $this->authentication->user($request);
            $userId = $user->getId();
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $title = $request->jsonBodyField('title');
            $text = $request->jsonBodyField('text');
            // Пытаемся создать объект статьи
            // из данных запроса
            $post = new Post(
                $userId,
                $title,
                $text,
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        // Логируем создание новой статьи
        $this->logger->info("Post created by: $userId");

        // Возвращаем успешный ответ,
        // содержащий id новой статьи
        return new SuccessfulResponse([
            'user_id' => $userId,
            'title' => $title,
            'text' => $text,
        ]);
    }
}
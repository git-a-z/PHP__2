<?php

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

// Класс реализует контракт действия
class FindById implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
    ) {}

    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $id = $request->query('id');
        } catch (HttpException $e) {
            // Если в запросе нет параметра id -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
            // Пытаемся найти пользователя в репозитории
            $post = $this->postsRepository->get($id);
        } catch (PostNotFoundException $e) {
            // Логируем id ненайденной статьи
            $this->logger->warning("Post: $id not found");
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'author_id' => $post->getAuthorId(),
            'title' => $post->getTitle(),
            'text' => $post->getText()
        ]);
    }
}
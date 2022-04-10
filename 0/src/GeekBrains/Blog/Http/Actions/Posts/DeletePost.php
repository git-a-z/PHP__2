<?php

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;

class DeletePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
    ) {}

    public function handle(Request $request): Response
    {
        // Пытаемся получить ID статьи из данных запроса
        try {
            $id = $request->query('id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Проверки на наличие статьи с таким ID здесь нет,
        // т.к. она уже есть в SqlitePostsRepository

        try {
            // Пытаемся удалить статью
            $this->postsRepository->delete($id);
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ, содержащий id удаленной статьи
        return new SuccessfulResponse([
            'id' => $id,
        ]);
    }
}
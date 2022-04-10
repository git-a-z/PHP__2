<?php

namespace Commands;

use GeekBrains\Blog\Commands\DummyLogger;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreateCommentCommand;
use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\SqliteCommentsRepository;
use PHPUnit\Framework\TestCase;

class CommentRepositoryTest extends TestCase
{
    // Тест, проверяющий, что команда сохраняет комментарий в репозитории
    /**
     * @throws ArgumentsException
     */
    public function testItSavesCommentToRepository(): void
    {
        // Создаём объект анонимного класса
        $commentsRepository = new class implements CommentsRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(Comment $comment): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(int $id): Comment
            {
                throw new CommentNotFoundException("Comment not found");
            }

            // Этого метода нет в контракте CommentsRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        // Передаём наш мок в команду
        $command = new CreateCommentCommand(
            $commentsRepository,
            new DummyLogger()
        );

        // Запускаем команду
        $command->handle(new Arguments([
            'post_id' => '1',
            'author_id' => '1',
            'text' => 'text',
        ]));

        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($commentsRepository->wasCalled());
    }

    // Тест, проверяющий, что репозиторий бросает исключение, если комментарий не найден.
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $repository = new SqliteCommentsRepository();
        $id = 0;

        // Описываем тип ожидаемого исключения
        $this->expectException(CommentNotFoundException::class);
        // и его сообщение
        $this->expectExceptionMessage("Cannot find comment: $id");

        // Проверяем утверждение
        $repository->get($id);
    }
}
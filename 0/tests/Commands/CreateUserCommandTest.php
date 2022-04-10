<?php

namespace Commands;

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\DummyLogger;
use GeekBrains\Blog\Exceptions\CommandException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    private function getAnonymousUsersRepository(): UsersRepositoryInterface {
        return new class implements UsersRepositoryInterface 
        {
            public function save(User $user): void {}

            public function get(int $id): User
            {
                throw new UserNotFoundException("User not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }

    // Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void
    {
        // $usersRepository - это объект анонимного класса,
        // реализующего контракт UsersRepositoryInterface
        $usersRepository = $this->getAnonymousUsersRepository();

        // Передаём объект анонимного класса
        // в качестве реализации UsersRepositoryInterface
        $command = new CreateUserCommand(
            $usersRepository,
            new DummyLogger()
        );

        // Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'ivan',
            'password' => 'ivan123',
        ]));
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresLastName(): void
    {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand(
            $this->getAnonymousUsersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'ivan',
            'password' => 'ivan123',
            // Нам нужно передать имя пользователя,
            // чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan'
        ]));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(int $id): User
            {
                throw new UserNotFoundException("User not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("User not found");
            }

            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        // Передаём наш мок в команду
        $command = new CreateUserCommand(
            $usersRepository,
            new DummyLogger()
        );

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'ivan',
            'password' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->getAnonymousUsersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'username' => 'ivan'
        ]));
    }
}
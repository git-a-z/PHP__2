<?php

namespace Commands;

use Exception;
use GeekBrains\Blog\Commands\Users\CreateUser;
use GeekBrains\Blog\Exceptions\CommandException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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

            public function update(User $user): void {}
        };
    }

    // Тест проверяет, что команда действительно требует имя пользователя
    /**
     * @throws Exception
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->getAnonymousUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    /**
     * @throws Exception
     */
    public function testItRequiresLastName(): void
    {
        // Тестируем новую команду
        $command = new CreateUser(
            $this->getAnonymousUsersRepository(),
        );

        // Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);

        // .. и его сообщение
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );

        // Запускаем команду методом run вместо handle
        $command->run(
            // Передаём аргументы как ArrayInput,
            // а не Arguments
            // Сами аргументы не меняются
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            // Передаём также объект,
            // реализующий контракт OutputInterface
            // Нам подойдёт реализация,
            // которая ничего не делает
            new NullOutput()
        );
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории

    /**
     * @throws ArgumentsException
     * @throws CommandException
     * @throws Exception
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

            public function update(User $user): void {}
        };

        $command = new CreateUser(
            $usersRepository
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }

    /**
     * @throws Exception
     */
    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->getAnonymousUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name, password")'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }
}
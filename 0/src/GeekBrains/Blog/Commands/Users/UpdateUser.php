<?php

namespace GeekBrains\Blog\Commands\Users;

use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'ID of a user to update'
            )
            ->addOption(
                // Имя опции
                'first-name',
                // Сокращённое имя
                'f',
                // Опция имеет значения
                InputOption::VALUE_OPTIONAL,
                // Описание
                'First name',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        // Получаем значения опций
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        // Выходим, если обе опции пусты
        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        // Получаем ID из аргумента
        $id = $input->getArgument('id');

        // Получаем пользователя из репозитория
        $user = $this->usersRepository->get($id);

        $updatedFirstName = empty($firstName)
            ? $user->getFirstName() : $firstName;

        $updatedLastName = empty($lastName)
            ? $user->getLastName() : $lastName;

        // Создаём новый объект пользователя
        $updatedUser = new User(
            // Имя пользователя и пароль
            // оставляем без изменений
            username: $user->getUsername(),
            hashedPassword: $user->getHashedPassword(),
            // Обновлённое имя
            firstName: $updatedFirstName,
            lastName: $updatedLastName
        );

        $updatedUser->setId($id);

        // Сохраняем обновлённого пользователя
        $this->usersRepository->update($updatedUser);
        $output->writeln("User updated: $id");

        return Command::SUCCESS;
    }
}
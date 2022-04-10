<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\SqliteUsersRepository;
use GeekBrains\Blog\User;

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository();

//Добавляем в репозиторий несколько пользователей
$usersRepository->save(new User(1, 'IvanNikitin@ya.ru', 'Ivan', 'Nikitin'));
$usersRepository->save(new User(2, 'AnnaPetrova@ya.ru', 'Anna', 'Petrova'));

try {
    echo $usersRepository->get(1);
} catch (UserNotFoundException $e) {
}

try {
    echo $usersRepository->getByUsername('AnnaPetrova@ya.ru');
} catch (UserNotFoundException $e) {
}
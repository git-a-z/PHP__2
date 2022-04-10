<?php

require_once 'vendor/autoload.php';

use GeekBrains\Blog\Repositories\MemoryUserRepository;
use GeekBrains\Blog\User;
use GeekBrains\Blog\Exceptions\UserNotFoundException;

//Создаём объект репозитория
$userRepository = new MemoryUserRepository();

//Добавляем в репозиторий несколько пользователей
$userRepository->save(new User(123, 'IvanNikitin@ya.ru', 'Ivan', 'Nikitin'));
$userRepository->save(new User(234, 'AnnaPetrova@ya.ru', 'Anna', 'Petrova'));

try {
    //Загружаем пользователя из репозитория
    $user = $userRepository->get(234);
    print $user;
} catch (UserNotFoundException $e) {
    print $e->getMessage();
}
<?php

use GeekBrains\Blog\Commands\FakeData\PopulateDB;
use GeekBrains\Blog\Commands\Posts\DeletePost;
use GeekBrains\Blog\Commands\Users\CreateUser;
use GeekBrains\Blog\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

// Запускаем приложение
try {
    $application->run();
} catch (Exception $e) {
}
<?php

use GeekBrains\Blog\Repositories\RepositoryFactory;
use GeekBrains\Blog\Commands\CommandFactory;
use GeekBrains\Blog\Commands\Arguments;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $repositoryFactory = $container->get(RepositoryFactory::class);
    $repository = $repositoryFactory->create($argv[1]);
    $commandFactory = $container->get(CommandFactory::class);
    $command = $commandFactory->create($argv[1], $repository, $logger);
    $command->handle(Arguments::fromArgv($argv));
} catch (Throwable $e) {
    echo "{$e->getMessage()}\n";
    // Логируем информацию об исключении.
    // Объект исключения передаётся логгеру
    // с ключом "exception".
    // Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}
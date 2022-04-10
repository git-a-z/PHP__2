<?php

namespace GeekBrains\Blog\Container;

use GeekBrains\Traits\Instance;
use Psr\Container\ContainerInterface;
use ReflectionClass;

// Контейнер реализует контракт, отписанный в PSR-11
class DIContainer implements ContainerInterface
{
    use Instance;

    private function __construct() {}

    // Массив правил создания объектов
    private array $resolvers = [];

    // Теперь правилами могут быть
    // не только строки (имена классов), но и объекты
    // Так что убираем указание типа у второго аргумента
    // и заодно переименовываем его в $resolver
    public function bind(string $id, $resolver)
    {
        $this->resolvers[$id] = $resolver;
    }

    /**
     * @throws NotFoundException
     */
    public function get(string $id): object
    {
        // Если есть правило для создания объекта типа $id,
        // (например, $id имеет значение
        // 'GeekBrains\.\.\UsersRepositoryInterface')
        if (array_key_exists($id, $this->resolvers)) {
            // .. тогда мы будем создавать объект того класса,
            // который указан в правиле
            // (например, 'GeekBrains\.\.\SqliteUsersRepository')
            $typeToCreate = $this->resolvers[$id];

            // Если в контейнере для запрашиваемого типа
            // уже есть готовый объект — возвращаем его
            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            // Вызываем тот же самый метод контейнера
            // и передаём в него имя класса, указанного в правиле
            return $this->get($typeToCreate);
        }

        if (!class_exists($id)) {
            throw new NotFoundException("Cannot resolve type: $id");
        }

        // Создаём объект рефлексии для запрашиваемого класса
        $reflectionClass = new ReflectionClass($id);

        // Исследуем конструктор класса
        $constructor = $reflectionClass->getConstructor();

        // Если конструктора нет - просто создаём объект нужного класса
        if (null === $constructor) {
            return new $id();
        }

        // В этот массив мы будем собирать объекты зависимостей класса
        $parameters = [];

        // Проходим по всем параметрам конструктора (зависимостям класса)
        foreach ($constructor->getParameters() as $parameter) {
            // Узнаем тип параметра конструктора (тип зависимости)
            $parameterType = $parameter->getType()->getName();

            // Получаем объект зависимости из контейнера
            $parameters[] = $this->get($parameterType);
        }

        // Создаём объект нужного нам типа с параметрами
        return new $id(...$parameters);
    }

    // Метод has из PSR-11
    public function has(string $id): bool
    {
        // Здесь мы просто пытаемся создать
        // объект требуемого типа
        try {
            $this->get($id);
        } catch (NotFoundException) {
            // Возвращаем false, если объект не создан...
            return false;
        }
        // и true, если создан
        return true;
    }
}
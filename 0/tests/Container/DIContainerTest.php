<?php

namespace Container;

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Container\NotFoundException;
use GeekBrains\Blog\Repositories\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        // Создаём объект контейнера
        $container = DIContainer::getInstance();
        // Описываем ожидаемое исключение
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Container\SomeClass'
        );
        // Пытаемся получить объект несуществующего класса
        $container->get(SomeClass::class);
    }

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassWithoutDependencies(): void
    {
        // Создаём объект контейнера
        $container = DIContainer::getInstance();
        // Пытаемся получить объект класса без зависимостей
        $object = $container->get(SomeClassWithoutDependencies::class);
        // Проверяем, что объект, который вернул контейнер,
        // имеет желаемый тип
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassByContract(): void
    {
        // Создаём объект контейнера
        $container = DIContainer::getInstance();

        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // создать объект, реализующий контракт
        // UsersRepositoryInterface, он возвращал бы
        // объект класса SqliteUsersRepository
        $container->bind(
            UsersRepositoryInterface::class,
            SqliteUsersRepository::class
        );

        // Пытаемся получить объект класса,
        // реализующего контракт UsersRepositoryInterface
        $object = $container->get(UsersRepositoryInterface::class);

        // Проверяем, что контейнер вернул
        // объект класса SqliteUsersRepository
        $this->assertInstanceOf(
            SqliteUsersRepository::class,
            $object
        );
    }

    /**
     * @throws NotFoundException
     */
    public function testItReturnsPredefinedObject(): void
    {
        // Создаём объект контейнера
        $container = DIContainer::getInstance();

        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // вернуть объект типа SomeClassWithParameter,
        // он возвращал бы предопределённый объект
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа SomeClassWithParameter
        $object = $container->get(SomeClassWithParameter::class);

        // Проверяем, что контейнер вернул
        // объект того же типа
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        // Проверяем, что контейнер вернул
        // тот же самый объект
        $this->assertSame(42, $object->value());
    }

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassWithDependencies(): void
    {
        // Создаём объект контейнера
        $container = DIContainer::getInstance();

        // Устанавливаем правило получения
        // объекта типа SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(ClassDependingOnAnother::class);

        // Проверяем, что контейнер вернул объект нужного нам типа
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }
}
<?php

namespace GeekBrains\Blog\Http;

use JetBrains\PhpStorm\ArrayShape;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;

//    private ?int $id = null;
//    protected ?string $reason = 'Something goes wrong+';

    // Неуспешный ответ содержит строку с причиной неуспеха,
    // по умолчанию - 'Something goes wrong'
    public function __construct(
        protected string $reason = 'Something goes wrong'
    ) {}

    // Реализация абстрактного метода
    // родительского класса
    #[ArrayShape(['reason' => "string"])] protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}
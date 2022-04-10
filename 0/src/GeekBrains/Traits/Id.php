<?php

namespace GeekBrains\Traits;

trait Id
{
    private ?int $id = null;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
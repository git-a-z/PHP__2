<?php

namespace GeekBrains\Person;

use DateTimeImmutable;

class Person
{
    public function __construct(
        private Name $name,
        private DateTimeImmutable $registeredOn
    ) {}

    public function __toString()
    {
        return sprintf('%s (на сайте с %s)', 
            $this->name, $this->registeredOn->format('Y-m-d'));
    }
}
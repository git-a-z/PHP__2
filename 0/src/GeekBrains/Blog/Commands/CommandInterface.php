<?php

namespace GeekBrains\Blog\Commands;

interface CommandInterface
{
    public function handle(Arguments $arguments): void;
}
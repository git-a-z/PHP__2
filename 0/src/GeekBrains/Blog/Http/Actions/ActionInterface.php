<?php

namespace GeekBrains\Blog\Http\Actions;

use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
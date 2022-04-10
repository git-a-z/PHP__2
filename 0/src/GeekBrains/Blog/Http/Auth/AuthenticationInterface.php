<?php

namespace GeekBrains\Blog\Http\Auth;

use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\User;

interface AuthenticationInterface
{
    // Контракт описывает единственный метод,
    // получающий пользователя из запроса
    public function user(Request $request): User;
}
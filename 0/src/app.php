<?php

spl_autoload_register(function ($class) {
    echo $class.'<br>';
    $DS = DIRECTORY_SEPARATOR;
    $dirs = explode('\\', $class);
    $className = str_replace('_', $DS, array_pop($dirs));
    echo $file = sprintf('src/%s.php', implode($DS, array_merge($dirs, [$className])));

    if (file_exists($file)) {
        require $file;
    }
});

use GeekBrains\Blog\Post;
use GeekBrains\Person_Test\Person_Test as Test;

$post = new Post(
    1,
    1,
    'Заголовок',
    'Всем привет!'
    // new Test()
);

print $post;
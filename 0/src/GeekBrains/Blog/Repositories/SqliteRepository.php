<?php

namespace GeekBrains\Blog\Repositories;

use GeekBrains\config\SqliteConfig;
use PDO;

class SqliteRepository implements RepositoryInterface
{
    protected PDO $connection;

    public function __construct() 
    {
        $this->connection = new PDO(SqliteConfig::DSN);
    }    
}
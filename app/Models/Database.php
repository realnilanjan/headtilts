<?php

namespace App\Models;

class Database {
    protected \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
}
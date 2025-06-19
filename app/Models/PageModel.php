<?php
// app/Models/PageModel.php

namespace App\Models;

class PageModel extends Database
{
    public function getAllPages()
    {
        $stmt = $this->pdo->query("SELECT * FROM pages");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPageBySlug($slug)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
<?php

namespace App\Models;

use App\Models\Database;

class MenuModel extends Database
{
    public function getAllMenus()
    {
        $stmt = $this->pdo->query("SELECT id, name, slug FROM menus ORDER BY name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMenuById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM menus WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function createMenu(array $data)
    {
        $name = $data['name'] ?? '';
        $slug = $data['slug'] ?? strtolower(trim(preg_replace('/[^a-z0-9-]+/i', '-', $name), '-'));

        $stmt = $this->pdo->prepare("INSERT INTO menus (name, slug) VALUES (?, ?)");
        return $stmt->execute([$name, $slug]);
    }

    public function getMenuItems($menu_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM menu_items WHERE menu_id = ? ORDER BY `order` ASC");
        $stmt->execute([$menu_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function saveMenuItem(array $data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO menu_items (menu_id, parent_id, title, url, type, object_id, `order`)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['menu_id'],
            $data['parent_id'] ?? null,
            $data['title'],
            $data['url'],
            $data['type'] ?? 'custom',
            $data['object_id'] ?? null,
            $data['order'] ?? 0
        ]);
    }

    public function getMenuBySlug($slug)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM menus WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getMenuSlugByID($id)
    {
        $stmt = $this->pdo->prepare("SELECT slug FROM menus WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}

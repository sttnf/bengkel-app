<?php

namespace App\Models;

use App\Core\Model;

class Service extends Model
{

    protected $table = 'services';

    public function getAllActive()
    {
        return $this->db->query("SELECT * FROM services WHERE is_active = 1")->fetchAll();
    }

    public function findByCategory(int $categoryId)
    {
        return $this->db->query("SELECT * FROM services WHERE category_id = :category_id AND is_active = 1", [
            'category_id' => $categoryId
        ])->fetchAll();
    }

    public function deactivate(int $id)
    {
        return $this->db->query("UPDATE services SET is_active = 0 WHERE service_id = :id", [
            'id' => $id
        ]);
    }

    public function findByName(string $name)
    {
        return $this->db->query("SELECT * FROM services WHERE name = :name", [
            'name' => $name
        ])->fetch();
    }
}

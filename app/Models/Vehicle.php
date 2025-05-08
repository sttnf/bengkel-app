<?php


namespace App\Models;

use App\Core\Model;

class Vehicle extends Model
{

    protected $table = 'customer_vehicles';

    public function getByUserId(int $userId): array
    {
        return $this->db->query(
            "SELECT cv.*, 
                    v.brand, 
                    v.model 
             FROM {$this->table} cv
             JOIN vehicles v ON cv.vehicle_id = v.id
             WHERE cv.user_id = :user_id
             ORDER BY cv.id DESC",
            ['user_id' => $userId]
        )->fetchAll();
    }

}

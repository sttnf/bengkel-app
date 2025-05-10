<?php

namespace App\Models;

use App\Core\Model;

class Inventory extends Model
{

    protected $table = 'inventory_items';

    public function getLowerStockItems(): array
    {
        return $this->findBy(
            ['current_stock <= reorder_level'],
            ['current_stock' => 'ASC'],
            5
        );
    }

    public function countLowStock()
    {
        return $this->db->query(
            "SELECT COUNT(*) as total FROM inventory_items WHERE current_stock <= reorder_level"
        )->fetchColumn();
    }

    public function countOutOfStock()
    {
        return $this->db->query(
            "SELECT COUNT(*) as total FROM inventory_items WHERE current_stock = 0"
        )->fetchColumn();
    }
}

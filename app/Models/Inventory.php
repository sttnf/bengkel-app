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
}

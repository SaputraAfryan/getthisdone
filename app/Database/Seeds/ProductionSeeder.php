<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run()
    {
        // Sample machines
        $machines = [
            ['name' => 'CNC Machine 1', 'description' => 'High precision CNC machine for metal cutting'],
            ['name' => 'Assembly Line A', 'description' => 'Automated assembly line for product assembly'],
            ['name' => 'Quality Control Station', 'description' => 'Quality inspection and testing station'],
            ['name' => 'Packaging Machine', 'description' => 'Automated packaging and labeling machine'],
            ['name' => 'Injection Molding Machine', 'description' => 'Plastic injection molding machine'],
        ];

        $this->db->table('machines')->insertBatch($machines);

        // Sample items
        $items = [
            ['name' => 'Widget A', 'code' => 'WGT-001', 'is_active' => true],
            ['name' => 'Component B', 'code' => 'CMP-002', 'is_active' => true],
            ['name' => 'Assembly C', 'code' => 'ASM-003', 'is_active' => true],
            ['name' => 'Part D', 'code' => 'PRT-004', 'is_active' => true],
            ['name' => 'Module E', 'code' => 'MOD-005', 'is_active' => true],
        ];

        $this->db->table('items')->insertBatch($items);

        // Sample item-machine relationships
        $itemMachines = [
            ['item_id' => 1, 'machine_id' => 1, 'production_capacity' => 100.00],
            ['item_id' => 1, 'machine_id' => 2, 'production_capacity' => 150.00],
            ['item_id' => 2, 'machine_id' => 1, 'production_capacity' => 80.00],
            ['item_id' => 2, 'machine_id' => 3, 'production_capacity' => 200.00],
            ['item_id' => 3, 'machine_id' => 2, 'production_capacity' => 120.00],
            ['item_id' => 3, 'machine_id' => 4, 'production_capacity' => 90.00],
            ['item_id' => 4, 'machine_id' => 5, 'production_capacity' => 75.00],
            ['item_id' => 5, 'machine_id' => 1, 'production_capacity' => 110.00],
        ];

        $this->db->table('item_machines')->insertBatch($itemMachines);
    }
}
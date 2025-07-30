<?php

namespace App\Models;

use CodeIgniter\Model;

class MachineModel extends Model
{
    protected $table = 'machines';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $allowedFields = ['name', 'description'];
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'description' => 'max_length[1000]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Machine name is required',
            'min_length' => 'Machine name must be at least 3 characters',
            'max_length' => 'Machine name cannot exceed 255 characters'
        ],
        'description' => [
            'max_length' => 'Description cannot exceed 1000 characters'
        ]
    ];
    protected $skipValidation = false;

    /**
     * Get machines with their production capacity info
     */
    public function getMachinesWithCapacity()
    {
        return $this->select('machines.*, COUNT(item_machines.id) as total_items, AVG(item_machines.production_capacity) as avg_capacity')
            ->join('item_machines', 'item_machines.machine_id = machines.id', 'left')
            ->where('machines.deleted_at', null)
            ->groupBy('machines.id')
            ->findAll();
    }
}

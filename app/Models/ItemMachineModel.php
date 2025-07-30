<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemMachineModel extends Model
{
    protected $table = 'item_machines';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['item_id', 'machine_id', 'production_capacity'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [
        'item_id' => 'required|integer',
        'machine_id' => 'required|integer',
        'production_capacity' => 'required|decimal'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get item-machine relationships with item and machine details
     */
    public function getItemMachinesWithDetails($limit = null, $offset = null)
    {
        $builder = $this->select('item_machines.*, items.name as item_name, items.code as item_code, machines.name as machine_name')
            ->join('items', 'items.id = item_machines.item_id', 'left')
            ->join('machines', 'machines.id = item_machines.machine_id', 'left')
            ->where('item_machines.deleted_at', null);

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Count total records for pagination
     */
    public function countItemMachines($search = '')
    {
        $builder = $this->select('item_machines.id')
            ->join('items', 'items.id = item_machines.item_id', 'left')
            ->join('machines', 'machines.id = item_machines.machine_id', 'left')
            ->where('item_machines.deleted_at', null);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('items.name', $search)
                ->orLike('items.code', $search)
                ->orLike('machines.name', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}

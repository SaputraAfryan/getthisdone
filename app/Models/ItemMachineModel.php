<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemMachineModel extends Model
{
    protected $table = 'item_machines';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'machine_id', 'production_capacity'];
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}

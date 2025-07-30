<?php

namespace App\Models;

use CodeIgniter\Model;

class MachineModel extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'price'];
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}

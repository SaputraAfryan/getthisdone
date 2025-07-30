<?php
namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields = ['name', 'code', 'is_active'];
}

<?php
namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $allowedFields = ['name', 'code', 'is_active'];
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'code' => 'required|min_length[3]|max_length[100]|is_unique[items.code,id,{id}]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Item name is required',
            'min_length' => 'Item name must be at least 3 characters',
            'max_length' => 'Item name cannot exceed 255 characters'
        ],
        'code' => [
            'required' => 'Item code is required',
            'min_length' => 'Item code must be at least 3 characters',
            'max_length' => 'Item code cannot exceed 100 characters',
            'is_unique' => 'Item code already exists'
        ]
    ];
    protected $skipValidation = false;

    /**
     * Get active items only
     */
    public function getActiveItems($limit = null, $offset = null)
    {
        $builder = $this->where('is_active', true);

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Search items by name or code
     */
    public function searchItems($search, $limit = null, $offset = null)
    {
        $builder = $this->where('is_active', true)
            ->groupStart()
            ->like('name', $search)
            ->orLike('code', $search)
            ->groupEnd();

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }
}

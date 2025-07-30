<?php
namespace App\Controllers;

use App\Models\ItemMachineModel;
use App\Models\ItemModel;
use App\Models\MachineModel;

class ItemMachineController extends BaseController
{
    protected $itemMachineModel;
    protected $itemModel;
    protected $machineModel;

    public function __construct()
    {
        $this->itemMachineModel = new ItemMachineModel();
        $this->itemModel = new ItemModel();
        $this->machineModel = new MachineModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Production Management'
        ];
        return view('production/index', $data);
    }

    public function ajax()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $request = $this->request;
            $columns = ['item_machines.id', 'items.name'];
            
            $search = $request->getPost('search')['value'] ?? '';
            $start = intval($request->getPost('start') ?? 0);
            $length = intval($request->getPost('length') ?? 10);
            
            $order = $request->getPost('order');
            $orderColumnIndex = intval($order[0]['column'] ?? 0);
            $orderColumn = $columns[$orderColumnIndex] ?? 'item_machines.id';
            $orderDir = ($order[0]['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

            // Get total count
            $total = $this->itemMachineModel->countItemMachines();
            
            // Get filtered count
            $filtered = $this->itemMachineModel->countItemMachines($search);
            
            // Get data with details
            $data = $this->itemMachineModel->getItemMachinesWithDetails($length, $start);

            // Apply search filter to the query if needed
            if (!empty($search)) {
                $builder = $this->itemMachineModel->select('item_machines.*, items.name as item_name, items.code as item_code, machines.name as machine_name')
                                                 ->join('items', 'items.id = item_machines.item_id', 'left')
                                                 ->join('machines', 'machines.id = item_machines.machine_id', 'left')
                                                 ->where('item_machines.deleted_at', null)
                                                 ->groupStart()
                                                 ->like('items.name', $search)
                                                 ->orLike('items.code', $search)
                                                 ->orLike('machines.name', $search)
                                                 ->groupEnd()
                                                 ->orderBy($orderColumn, $orderDir)
                                                 ->limit($length, $start);
                
                $data = $builder->get()->getResultArray();
            }

            return $this->response->setJSON([
                'draw' => intval($request->getPost('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => array_map(function($item) {
                    return [
                        'id' => $item['id'],
                        'item_name' => esc($item['item_name'] ?? 'Unknown Item'),
                        'item_code' => esc($item['item_code'] ?? ''),
                        'machine_name' => esc($item['machine_name'] ?? 'Unknown Machine'),
                        'production_capacity' => number_format($item['production_capacity'] ?? 0, 2),
                        'created_at' => $item['created_at'] ? date('Y-m-d H:i:s', strtotime($item['created_at'])) : '-'
                    ];
                }, $data)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'ItemMachineController::ajax error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Internal server error']);
        }
    }
}

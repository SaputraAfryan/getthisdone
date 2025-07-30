<?php
namespace App\Controllers;

use App\Models\MachineModel;

class MachineController extends BaseController
{
    protected $machineModel;

    public function __construct()
    {
        $this->machineModel = new MachineModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Machine Management'
        ];
        return view('machine/index', $data);
    }

    public function ajax()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $request = $this->request;
            $columns = ['id', 'name'];
            
            $search = $request->getPost('search')['value'] ?? '';
            $start = intval($request->getPost('start') ?? 0);
            $length = intval($request->getPost('length') ?? 10);
            
            $order = $request->getPost('order');
            $orderColumnIndex = intval($order[0]['column'] ?? 0);
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = ($order[0]['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

            $builder = $this->machineModel;
            
            if (!empty($search)) {
                $builder = $builder->like('name', $search);
            }

            $total = $this->machineModel->countAll();
            $filtered = $builder->countAllResults(false);
            
            $data = $builder->orderBy($orderColumn, $orderDir)->findAll($length, $start);

            return $this->response->setJSON([
                'draw' => intval($request->getPost('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => array_map(function($item) {
                    return [
                        'id' => $item['id'],
                        'item_name' => esc($item['name']),
                        'description' => esc($item['description'] ?? ''),
                        'created_at' => $item['created_at'] ? date('Y-m-d H:i:s', strtotime($item['created_at'])) : '-'
                    ];
                }, $data)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'MachineController::ajax error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Internal server error']);
        }
    }
}

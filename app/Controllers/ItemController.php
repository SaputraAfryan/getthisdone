<?php
namespace App\Controllers;

use App\Models\ItemModel;
use CodeIgniter\HTTP\ResponseInterface;

class ItemController extends BaseController
{
    protected $itemModel;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Item Management'
        ];
        return view('item/index', $data);
    }

    public function ajax()
    {
        try {
            $request = $this->request;

            // Validate request
            if (!$request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $columns = ['id', 'id', 'name', 'code', 'updated_at'];

            $builder = $this->itemModel->where('is_active', true);

            // Handle column search
            $columns_search = $request->getPost('columns');
            if (!empty($columns_search[2]['search']['value'])) {
                $builder->like('name', $columns_search[2]['search']['value']);
            }

            if (!empty($columns_search[3]['search']['value'])) {
                $builder->like('code', $columns_search[3]['search']['value']);
            }

            // Global search
            $search = $request->getPost('search');
            if (!empty($search['value'])) {
                $builder->groupStart()
                    ->like('name', $search['value'])
                    ->orLike('code', $search['value'])
                    ->groupEnd();
            }

            $total = $builder->countAllResults(false);

            // Handle ordering
            $order = $request->getPost('order');
            if (!empty($order)) {
                $orderColumnIndex = intval($order[0]['column']);
                $orderDirection = $order[0]['dir'] === 'desc' ? 'desc' : 'asc';
                if (isset($columns[$orderColumnIndex])) {
                    $builder->orderBy($columns[$orderColumnIndex], $orderDirection);
                }
            }

            // Handle pagination
            $start = intval($request->getPost('start') ?? 0);
            $length = intval($request->getPost('length') ?? 10);
            $builder->limit($length, $start);

            $data = $builder->get()->getResultArray();

            $result = [];
            $no = $start + 1;

            foreach ($data as $row) {
                $lastUpdate = $row['updated_at'] ?: $row['created_at'];

                $result[] = [
                    'no' => $no++,
                    'name' => esc($row['name']),
                    'code' => esc($row['code']),
                    'last_update' => $lastUpdate ? date('Y-m-d H:i:s', strtotime($lastUpdate)) : '-',
                    'action' => view('item/_action', ['id' => $row['id']]),
                ];
            }

            return $this->response->setJSON([
                'draw' => intval($request->getPost('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            // dd($e);
            log_message('error', 'ItemController::ajax error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Internal server error']);
        }
    }

    public function store()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $data = $this->request->getPost();
            dd($data);

            // Sanitize input
            $data['name'] = trim($data['name'] ?? '');
            $data['code'] = trim($data['code'] ?? '');
            $data['is_active'] = true;

            if (!$this->itemModel->save($data)) {
                return $this->response->setJSON([
                    'status' => false,
                    'errors' => $this->itemModel->errors()
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => !empty($data['id']) ? 'Item updated successfully' : 'Item created successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'ItemController::store error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Internal server error']);
        }
    }

    public function delete($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $id = intval($id);
            if ($id <= 0) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid ID']);
            }

            $item = $this->itemModel->find($id);
            if (!$item) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
            }

            if (!$this->itemModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Failed to delete item'
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Item deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'ItemController::delete error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Internal server error']);
        }
    }

    public function get($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
            }

            $id = intval($id);
            if ($id <= 0) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid ID']);
            }

            $item = $this->itemModel->find($id);
            if (!$item) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
            }

            return $this->response->setJSON($item);

        } catch (\Exception $e) {
            log_message('error', 'ItemController::get error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Internal server error']);
        }
    }
}

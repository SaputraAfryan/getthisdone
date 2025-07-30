<?php
namespace App\Controllers;

use App\Models\ItemModel;
use CodeIgniter\Controller;

class ItemController extends BaseController
{
    public function index()
    {
        return view('item/index');
    }

    public function ajax()
    {
        $request = service('request');
        $model = new ItemModel();

        $columns = ['id', 'id', 'name', 'code', 'updated_at'];

        $builder = $model->where('is_active', true);

        if (!empty($request->getPost('columns')[2]['search']['value'])) {
            $builder->like('name', $request->getPost('columns')[1]['search']['value']);
        }

        if (!empty($request->getPost('columns')[3]['search']['value'])) {
            $builder->like('code', $request->getPost('columns')[2]['search']['value']);
        }

        $total = $builder->countAllResults(false);

        $orderColumnIndex = $request->getPost('order')[0]['column'];
        $orderDirection = $request->getPost('order')[0]['dir'];
        $builder->orderBy($columns[$orderColumnIndex], $orderDirection);

        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {
            $lastUpdate = $row['updated_at'] ?: $row['created_at'];

            $result[] = [
                'no' => $no++,
                'name' => $row['name'],
                'code' => $row['code'],
                'last_update' => date('Y-m-d H:i:s', strtotime($lastUpdate)),
                'action' => view('item/_action', ['id' => $row['id']]),
            ];
        }

        return $this->response->setJSON([
            'draw' => $request->getPost('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $result,
        ]);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'code' => 'required|is_unique[items.code,id,{id}]'
        ]);

        $data = $this->request->getPost();
        if (!$validation->run($data)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ItemModel();
        $model->save([
            'id' => $data['id'] ?? null, // untuk update jika ada ID
            'name' => $data['name'],
            'code' => $data['code'],
        ]);

        return $this->response->setJSON(['status' => true]);
    }

    public function delete($id)
    {
        $model = new ItemModel();
        $model->delete($id); // soft delete
        return $this->response->setJSON(['status' => true]);
    }

    public function get($id)
    {
        $model = new ItemModel();
        $item = $model->find($id);
        return $this->response->setJSON($item);
    }
}

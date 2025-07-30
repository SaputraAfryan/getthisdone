<?php
namespace App\Controllers;

use App\Models\ItemModel;

class ItemMachineController extends BaseController
{
    public function index()
    {
        return view('production/index');
    }

    public function ajax()
    {
        $model = new ItemModel();
        $request = service('request');

        $columns = ['id', 'item_name'];
        $search = $request->getPost('search')['value'] ?? '';
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $orderColumn = $columns[$request->getPost('order')[0]['column'] ?? 0];
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'asc';

        if ($search) {
            $model->like('item_name', $search);
        }

        $total = $model->countAll();
        $filtered = $model->countAllResults(false);
        $data = $model->orderBy($orderColumn, $orderDir)->findAll($length, $start);

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ]);
    }
}

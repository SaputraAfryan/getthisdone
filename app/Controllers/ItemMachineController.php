<?php
namespace App\Controllers;

use App\Models\ItemMachineModel;

class ItemMachineController extends BaseController
{
    public function index()
    {
        return view('production/index');
    }

    public function ajax()
    {
        $model = new ItemMachineModel();
        $request = service('request');

        $columns = ['id', 'name'];
        $search = $request->getPost('search')['value'] ?? '';
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $orderColumn = $columns[$request->getPost('order')[0]['column'] ?? 0];
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'asc';

        if ($search) {
            $model->like('name', $search);
        }

        $total = $model->countAll();
        $filtered = $model->countAllResults(false);
        $data = $model->orderBy($orderColumn, $orderDir)->findAll($length, $start);

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'item_name' => $item['name'] // Map name to item_name for consistency
                ];
            }, $data)
        ]);
    }
}

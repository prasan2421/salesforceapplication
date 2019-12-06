<?php
namespace App\Helpers;

use App\Helpers\DataTableAbstract;

class EloquentDataTable extends DataTableAbstract {
	protected $query = null;

	public function __construct($query) {
		$this->query = $query;
	}

    public function make() {
        $request = request();

        $recordsTotal = $this->query->count();

        if($request->has('search') && $request->search['value'] != '') {
            $str = $request->search['value'];

            $this->query->where(function($query) use ($request, $str) {
                foreach($request->columns as $column) {
                    if($column['searchable']) {
                        $query->orWhere($column['name'], 'LIKE', '%' . $str . '%');
                        
                        // $arr = explode('.', $column['name']);
                        // if(count($arr) == 2) {
                        //     $query->orWhereHas($arr[0], function($query1) use ($arr, $str) {
                        //         $query1->where($arr[1], 'LIKE', '%' . $str . '%');
                        //     });
                        // }
                        // else {
                        //     $query->orWhere($column['name'], 'LIKE', '%' . $str . '%');
                        // }
                    }
                }
            });
        }

        $recordsFiltered = $this->query->count();

        if($request->has('order') && count($request->order)) {
            foreach($request->order as $order) {
                $columnIndex = intval($order['column']);
                $requestColumn = $request->columns[$columnIndex];

                if($requestColumn['orderable']) {
                    $this->query->orderBy($requestColumn['name'], $order['dir']);
                }
            }
        }

        if($request->has('start') && $request->length != -1) {
            $this->query->skip(intval($request->start))->take(intval($request->length));
        }
        
        $data = [];

        $result = $this->query->get();

        foreach($result as $row) {
            $item = $row->toArray();

            foreach($this->appendColumns as $appendColumn) {
                $item[$appendColumn['name']] = $appendColumn['content']($row);
            }

            $data[] = $item;
        }

        return json_encode([
            'draw' => $request->has('draw') ? intval($request->draw) : 0,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
}
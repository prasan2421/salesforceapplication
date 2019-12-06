<?php
namespace App\Helpers;

// use App\Helpers\DatabaseHelper;

use App\Helpers\DataTableAbstract;

use DB;

class AggregateDataTable extends DataTableAbstract {
    protected $collectionName = null;
    protected $pipeline = [];

	public function __construct($collectionName, $pipeline) {
		$this->collectionName = $collectionName;
        $this->pipeline = $pipeline;
	}

	public function make() {
		// $db = DatabaseHelper::getDatabase();

        $db = DB::getMongoDB();
        
        $collection = $db->selectCollection($this->collectionName);

        $request = request();

        $recordsTotal = 0;

        $results = $collection->aggregate(array_merge($this->pipeline, [ [ '$count' => 'count' ] ]));
        foreach($results as $row) {
            $recordsTotal = $row->count;
        }

        if($request->has('search') && $request->search['value'] != '') {
            $str = $request->search['value'];

            $queries = [];

            foreach($request->columns as $column) {
                if($column['searchable']) {
                    $queries[] = [
                        $column['name'] => [
                            '$regex' => $str,
                            '$options' => 'i'
                        ]
                    ];
                }
            }

            if(count($queries) > 0) {
                $this->pipeline[] = [
                    '$match' => [
                        '$or' => $queries
                    ]
                ];
            }
        }

        $recordsFiltered = 0;

        $results = $collection->aggregate(array_merge($this->pipeline, [ [ '$count' => 'count' ] ]));
        foreach($results as $row) {
            $recordsFiltered = $row->count;
        }

        if($request->has('order') && count($request->order)) {
            $sort = [];

            foreach($request->order as $order) {
                $columnIndex = intval($order['column']);
                $requestColumn = $request->columns[$columnIndex];

                if($requestColumn['orderable']) {
                    $sort[$requestColumn['name']] = $order['dir'] == 'asc' ? 1 : -1;
                }
            }

            if(count($sort) > 0) {
                $this->pipeline[] = [
                    '$sort' => $sort
                ];
            }
        }

        if($request->has('start') && $request->length != -1) {
            $this->pipeline[] = [ '$skip' => intval($request->start) ];
            $this->pipeline[] = [ '$limit' => intval($request->length) ];
        }

        $data = [];

        $results = $collection->aggregate($this->pipeline);

        foreach($results as $row) {
            $item = $row->getArrayCopy();

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
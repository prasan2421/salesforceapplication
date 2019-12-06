<?php
namespace App\Helpers;

use App\Helpers\EloquentDataTable;

use App\Helpers\AggregateDataTable;

class DataTableHelper {

	private function __construct() {

	}

	public static function of($query) {
		return new EloquentDataTable($query);
	}

    public static function aggregate($collection, $pipeline) {
        return new AggregateDataTable($collection, $pipeline);
    }
}
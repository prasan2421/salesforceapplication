<?php
namespace App\Helpers;

use \MongoDB;

class DatabaseHelper {
	public static function getDatabase() {
		$dbHost = env('DB_HOST');
        $dbPort = env('DB_PORT');
        $dbName = env('DB_DATABASE');

        $client = new MongoDB\Client('mongodb://' . $dbHost . ':' . $dbPort);
        $db = $client->selectDatabase($dbName);

        return $db;
	}
}
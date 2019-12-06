<?php
namespace App\Helpers;

abstract class DataTableAbstract {
	protected $appendColumns = [];

	public function addColumn($name, $content) {
		$this->appendColumns[] = [
			'name' => $name,
			'content' => $content
		];

		return $this;
	}

	abstract public function make();
}
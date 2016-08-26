<?php

namespace ExchangeRate\Model;

class Currency
{
	public $currencycode;
	public $name;

	public function exchangeArray(array $data)
	{
		$this->currencycode = !empty($data['currency']) ? $data['currency'] : null;
		$this->name = !empty($data['name']) ? $data['name'] : null;
	}
}
?>

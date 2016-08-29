<?php
namespace ExchangeRates\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class CurrencyTable
{
	private $tableGateway;

	public function __construct(TableGatewayInterface $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()
	{
		return $this->tableGateway->select();
	}

	public function getCurrency($code)
	{
		$rowset = $this->tableGateway->select(['currency' => $code]);
		$row = $rowset->current();
		if (! $row) {
			throw new RuntimeException(sprintf(
				'Could not find currency with code %s',
				$code
			));
		}
	}
}
?>
